// Compiler for PHP (aka KPHP)
// Copyright (c) 2021 LLC «V Kontakte»
// Distributed under the GPL v3 License, see LICENSE.notice.txt

#include "server/statshouse/statshouse-client.h"

#include <fcntl.h>
#include <unistd.h>

#include "common/resolver.h"
#include "common/server/stats.h"
#include "common/stats/provider.h"
#include "common/tl/constants/statshouse.h"
#include "common/tl/methods/string.h"
#include "net/net-connections.h"
#include "runtime/critical_section.h"
#include "server/server-log.h"

namespace {

class statshouse_stats_t : public stats_t {
public:
  explicit statshouse_stats_t(const std::vector<std::pair<std::string, std::string>> &tags)
    : tags(tags) {}

  void add_general_stat(const char *, const char *, ...) noexcept final {
    // ignore it
  }

  bool need_aggregated_stats() noexcept final {
    return false;
  }

  int get_counter() const {
    return counter;
  }

protected:
  void add_stat(char type [[maybe_unused]], const char *key, double value) noexcept final {
    auto metric = make_statshouse_value_metric(normalize_key(key, "_%s", stats_prefix), value, tags);
    auto len = vk::tl::store_to_buffer(sb.buff + sb.pos, sb.size, metric);
    sb.pos += len;
    ++counter;
  }

  void add_stat(char type, const char *key, long long value) noexcept final {
    add_stat(type, key, static_cast<double>(value));
  }

  void add_stat_with_tag_type(char type [[maybe_unused]], const char *key, const char *type_tag, double value) noexcept final {
    std::vector<std::pair<std::string, std::string>> metric_tags = {{"type", std::string(type_tag)}, {"host", std::string(kdb_gethostname())}};
    auto metric = make_statshouse_value_metric(normalize_key(key, "_%s", stats_prefix), value, metric_tags);
    auto len = vk::tl::store_to_buffer(sb.buff + sb.pos, sb.size, metric);
    sb.pos += len;
    ++counter;
  }

  void add_stat_with_tag_type(char type, const char *key, const char *type_tag, long long value) noexcept final {
    add_stat_with_tag_type(type, key, type_tag, static_cast<double>(value));
  }

  void add_multiple_stats(const char *key, std::vector<double> &&values) noexcept final {
    auto metric = make_statshouse_value_metrics(normalize_key(key, "_%s", stats_prefix), std::move(values), tags);
    auto len = vk::tl::store_to_buffer(sb.buff + sb.pos, sb.size - sb.pos, metric);
    sb.pos += len;
    ++counter;
  }

private:
  int counter{0};
  const std::vector<std::pair<std::string, std::string>> &tags;
};

std::pair<char *, int> prepare_statshouse_stats(statshouse_stats_t &&stats) {
  stats.stats_prefix = "kphp";
  char *buf = get_engine_default_prepare_stats_buffer();

  sb_init(&stats.sb, buf, STATS_BUFFER_LEN);
  constexpr int offset = 3 * sizeof(int32_t); // for magic, fields_mask and vector size
  stats.sb.pos = offset;
  prepare_common_stats_with_tag_mask(&stats, stats_tag_kphp_server);

  auto metrics_batch = StatsHouseAddMetricsBatch{.fields_mask = vk::tl::statshouse::add_metrics_batch_fields_mask::ALL, .metrics_size = stats.get_counter()};
  vk::tl::store_to_buffer(stats.sb.buff, offset, metrics_batch);
  return {buf, stats.sb.pos};
}
} // namespace

void StatsHouseClient::set_port(int value) {
  this->port = value;
}

void StatsHouseClient::set_host(std::string value) {
  this->host = std::move(value);
}

bool StatsHouseClient::init_connection() {
  if (sock_fd <= 0) {
    sock_fd = socket(AF_INET, SOCK_DGRAM, 0);
    if (sock_fd < 0) {
      log_server_error("Can't create statshouse socket");
      return false;
    }
  }
  fcntl(sock_fd, F_SETFL, O_NONBLOCK);

  hostent *h;
  std::string hostname = host.empty() ? "localhost" : host;
  if (!(h = gethostbyname(hostname.c_str())) || h->h_addrtype != AF_INET || h->h_length != 4 || !h->h_addr_list || !h->h_addr) {
    log_server_error("Can't resolve statshouse host: %s", host.c_str());
    return false;
  }
  struct sockaddr_in addr {};
  memset(&addr, 0, sizeof(addr));
  addr.sin_family = AF_INET;
  addr.sin_port = htons(port);
  addr.sin_addr.s_addr = (*reinterpret_cast<uint32_t *>(h->h_addr));
  if (connect(sock_fd, reinterpret_cast<sockaddr *>(&addr), sizeof(addr)) < 0) {
    log_server_error("Can't connect to statshouse host: %s", hostname.c_str());
    return false;
  }
  return true;
}

void StatsHouseClient::master_send_metrics() {
  auto [result, len] = prepare_statshouse_stats(statshouse_stats_t{tags});
  send_metrics(result, len);
}

void StatsHouseClient::send_metrics(char *result, int len) {
  if (port == 0 || (sock_fd <= 0 && !init_connection())) {
    return;
  }

  ssize_t slen = send(sock_fd, result, len, 0);
  if (slen < 0) {
    log_server_error("Can't send metrics to statshouse (len = %i): %s", len, strerror(errno));
  }
}

StatsHouseClient::StatsHouseClient() {
  if (const char *hostname = kdb_gethostname()) {
    tags = {{"host", std::string(hostname)}};
  } else {
    log_server_error("Can't gethostname for statshouse metrics: %s", strerror(errno));
  }
}

StatsHouseClient::~StatsHouseClient() {
  if (sock_fd > 0) {
    close(sock_fd);
  }
}
