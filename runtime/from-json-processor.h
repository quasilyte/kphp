// Compiler for PHP (aka KPHP)
// Copyright (c) 2020 LLC «V Kontakte»
// Distributed under the GPL v3 License, see LICENSE.notice.txt

#pragma once

#include <rapidjson/document.h>
#include <rapidjson/error/en.h>
#include <rapidjson/pointer.h>

#include "runtime/kphp_core.h"


class FromJsonVisitor {
public:
  explicit FromJsonVisitor(const rapidjson::Value &json) noexcept
    : json_(json) {}

  template<typename T>
  void operator()(std::string_view key, T &value) {
    static std::array<char, 1024> buffer = {};
    assert(key.size() < buffer.size() - 1);

    buffer.front() = '/';
    std::memcpy(buffer.data() + 1, key.data(), key.size());

    const auto *json_value = rapidjson::Pointer{buffer.data(), key.size() + 1}.Get(json_);
    if (!json_value || json_value->IsNull()) {
      return; //TODO: is it ok to just return when no needed key in json, or corresponding value for key is null?
    }
    do_set(value, *json_value);
  }

private:
  template<typename T>
  void do_set(T &value, const rapidjson::Value &json) {
    value = json.Get<T>();
  }

  void do_set(string &value, const rapidjson::Value &json) {
    value.assign(json.GetString(), json.GetStringLength());
  }

  template<typename T>
  void do_set(Optional<T> &value, const rapidjson::Value &json) {
    do_set(value.ref(), json);
  }

  template<typename I>
  void do_set(class_instance<I> &klass, const rapidjson::Value &json);

  void do_set(mixed &value, const rapidjson::Value &json) {
    if (json.IsNumber()) {
      do_set_number(value, json);
    } else if (json.IsBool()) {
      value = json.GetBool();
    } else if (json.IsString()) {
      value.assign(json.GetString(), json.GetStringLength());
    }
    // TODO: add array.
  }

  void do_set_number(mixed &value, const rapidjson::Value &json) {
    if (json.IsInt64()) {
      value = json.GetInt64();
    } else {
      value = json.GetDouble();
    }
  }

  const rapidjson::Value &json_;
};

template<typename ClassName>
ClassName from_json_impl(const rapidjson::Value &json) {
  ClassName instance;
  if constexpr (std::is_empty_v<typename ClassName::ClassType>) {
    instance.empty_alloc();
  } else {
    instance.alloc();
    FromJsonVisitor visitor{json};
    instance.get()->accept(visitor);
  }
  return instance;
}

template<typename I>
void FromJsonVisitor::do_set(class_instance<I> &klass, const rapidjson::Value &json) {
  klass = from_json_impl<class_instance<I>>(json);
}

template<typename ClassName>
ClassName f$from_json(const string &json_string, const string &/*class_mame*/) {
  rapidjson::Document json;
  json.Parse(json_string.c_str(), json_string.size());
  php_assert(!json.HasParseError());
  php_assert(json.IsObject());
  return from_json_impl<ClassName>(json.GetObject());
}
