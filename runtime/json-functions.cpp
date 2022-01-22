// Compiler for PHP (aka KPHP)
// Copyright (c) 2020 LLC «V Kontakte»
// Distributed under the GPL v3 License, see LICENSE.notice.txt

#include "runtime/json-functions.h"

#include "common/algorithms/find.h"

#include "runtime/exception.h"
#include "runtime/string_functions.h"

namespace {

void json_append_one_char(unsigned int c) noexcept {
  static_SB.append_char('\\');
  static_SB.append_char('u');
  static_SB.append_char("0123456789abcdef"[c >> 12]);
  static_SB.append_char("0123456789abcdef"[(c >> 8) & 15]);
  static_SB.append_char("0123456789abcdef"[(c >> 4) & 15]);
  static_SB.append_char("0123456789abcdef"[c & 15]);
}

bool json_append_char(unsigned int c) noexcept {
  if (c < 0x10000) {
    if (0xD7FF < c && c < 0xE000) {
      return false;
    }
    json_append_one_char(c);
    return true;
  }
  if (c <= 0x10ffff) {
    c -= 0x10000;
    json_append_one_char(0xD800 | (c >> 10));
    json_append_one_char(0xDC00 | (c & 0x3FF));
    return true;
  }
  return false;
}


bool do_json_encode_string_php(const char *s, int len, int64_t options) noexcept {
  int begin_pos = static_SB.size();
  if (options & JSON_UNESCAPED_UNICODE) {
    static_SB.reserve(2 * len + 2);
  } else {
    static_SB.reserve(6 * len + 2);
  }
  static_SB.append_char('"');

  auto fire_error = [begin_pos](int pos) {
    php_warning("Not a valid utf-8 character at pos %d in function json_encode", pos);
    static_SB.set_pos(begin_pos);
    static_SB.append("null", 4);
    return false;
  };

  for (int pos = 0; pos < len; pos++) {
    switch (s[pos]) {
      case '"':
        static_SB.append_char('\\');
        static_SB.append_char('"');
        break;
      case '\\':
        static_SB.append_char('\\');
        static_SB.append_char('\\');
        break;
      case '/':
        static_SB.append_char('\\');
        static_SB.append_char('/');
        break;
      case '\b':
        static_SB.append_char('\\');
        static_SB.append_char('b');
        break;
      case '\f':
        static_SB.append_char('\\');
        static_SB.append_char('f');
        break;
      case '\n':
        static_SB.append_char('\\');
        static_SB.append_char('n');
        break;
      case '\r':
        static_SB.append_char('\\');
        static_SB.append_char('r');
        break;
      case '\t':
        static_SB.append_char('\\');
        static_SB.append_char('t');
        break;
      case 0 ... 7:
      case 11:
      case 14 ... 31:
        json_append_one_char(s[pos]);
        break;
      case -128 ... -1: {
        const int a = s[pos];
        if ((a & 0x40) == 0) {
          return fire_error(pos);
        }

        const int b = s[++pos];
        if ((b & 0xc0) != 0x80) {
          return fire_error(pos);
        }
        if ((a & 0x20) == 0) {
          if ((a & 0x1e) <= 0) {
            return fire_error(pos);
          }
          if (options & JSON_UNESCAPED_UNICODE) {
            static_SB.append_char(static_cast<char>(a));
            static_SB.append_char(static_cast<char>(b));
          } else if (!json_append_char(((a & 0x1f) << 6) | (b & 0x3f))) {
            return fire_error(pos);
          }
          break;
        }

        const int c = s[++pos];
        if ((c & 0xc0) != 0x80) {
          return fire_error(pos);
        }
        if ((a & 0x10) == 0) {
          if (((a & 0x0f) | (b & 0x20)) <= 0) {
            return fire_error(pos);
          }
          if (options & JSON_UNESCAPED_UNICODE) {
            static_SB.append_char(static_cast<char>(a));
            static_SB.append_char(static_cast<char>(b));
            static_SB.append_char(static_cast<char>(c));
          } else if (!json_append_char(((a & 0x0f) << 12) | ((b & 0x3f) << 6) | (c & 0x3f))) {
            return fire_error(pos);
          }
          break;
        }

        const int d = s[++pos];
        if ((d & 0xc0) != 0x80) {
          return fire_error(pos);
        }
        if ((a & 0x08) == 0) {
          if (((a & 0x07) | (b & 0x30)) <= 0) {
            return fire_error(pos);
          }
          if (options & JSON_UNESCAPED_UNICODE) {
            static_SB.append_char(static_cast<char>(a));
            static_SB.append_char(static_cast<char>(b));
            static_SB.append_char(static_cast<char>(c));
            static_SB.append_char(static_cast<char>(d));
          } else if (!json_append_char(((a & 0x07) << 18) | ((b & 0x3f) << 12) | ((c & 0x3f) << 6) | (d & 0x3f))) {
            return fire_error(pos);
          }
          break;
        }

        return fire_error(pos);
      }
      default:
        static_SB.append_char(s[pos]);
        break;
    }
  }

  static_SB.append_char('"');
  return true;
}

bool do_json_encode_string_vkext(const char *s, int len) noexcept {
  static_SB.reserve(2 * len + 2);
  if (static_SB.string_buffer_error_flag == STRING_BUFFER_ERROR_FLAG_FAILED) {
    return false;
  }

  static_SB.append_char('"');

  for (int pos = 0; pos < len; pos++) {
    char c = s[pos];
    if (unlikely (static_cast<unsigned int>(c) < 32u)) {
      switch (c) {
        case '\b':
          static_SB.append_char('\\');
          static_SB.append_char('b');
          break;
        case '\f':
          static_SB.append_char('\\');
          static_SB.append_char('f');
          break;
        case '\n':
          static_SB.append_char('\\');
          static_SB.append_char('n');
          break;
        case '\r':
          static_SB.append_char('\\');
          static_SB.append_char('r');
          break;
        case '\t':
          static_SB.append_char('\\');
          static_SB.append_char('t');
          break;
      }
    } else {
      if (c == '"' || c == '\\' || c == '/') {
        static_SB.append_char('\\');
      }
      static_SB.append_char(c);
    }
  }

  static_SB.append_char('"');

  return true;
}

} // namespace

namespace impl_ {

JsonEncoder::JsonEncoder(int64_t options, bool simple_encode) noexcept:
  options_(options),
  simple_encode_(simple_encode) {
}

bool JsonEncoder::encode(bool b) const noexcept {
  if (b) {
    static_SB.append("true", 4);
  } else {
    static_SB.append("false", 5);
  }
  return true;
}

bool JsonEncoder::encode_null() const noexcept {
  static_SB.append("null", 4);
  return true;
}

bool JsonEncoder::encode(int64_t i) const noexcept {
  static_SB << i;
  return true;
}

bool JsonEncoder::encode(double d) const noexcept {
  if (vk::any_of_equal(std::fpclassify(d), FP_INFINITE, FP_NAN)) {
    php_warning("strange double %lf in function json_encode", d);
    if (options_ & JSON_PARTIAL_OUTPUT_ON_ERROR) {
      static_SB.append("0", 1);
    } else {
      return false;
    }
  } else {
    static_SB << (simple_encode_ ? f$number_format(d, 6, string{"."}, string{}) : string{d});
  }
  return true;
}

bool JsonEncoder::encode(const string &s) const noexcept {
  return simple_encode_ ? do_json_encode_string_vkext(s.c_str(), s.size()) : do_json_encode_string_php(s.c_str(), s.size(), options_);
}

bool JsonEncoder::encode(const mixed &v) const noexcept {
  switch (v.get_type()) {
    case mixed::type::NUL:
      return encode_null();
    case mixed::type::BOOLEAN:
      return encode(v.as_bool());
    case mixed::type::INTEGER:
      return encode(v.as_int());
    case mixed::type::FLOAT:
      return encode(v.as_double());
    case mixed::type::STRING:
      return encode(v.as_string());
    case mixed::type::ARRAY:
      return encode(v.as_array());
    default:
      __builtin_unreachable();
  }
}

} // namespace impl_

namespace {

void json_skip_blanks(const char *s, int &i) noexcept {
  while (vk::any_of_equal(s[i], ' ', '\t', '\r', '\n')) {
    i++;
  }
}

bool do_json_decode(const char *s, int s_len, int &i, mixed &v) noexcept {
  if (!v.is_null()) {
    v.destroy();
  }
  json_skip_blanks(s, i);
  switch (s[i]) {
    case 'n':
      if (s[i + 1] == 'u' &&
          s[i + 2] == 'l' &&
          s[i + 3] == 'l') {
        i += 4;
        return true;
      }
      break;
    case 't':
      if (s[i + 1] == 'r' &&
          s[i + 2] == 'u' &&
          s[i + 3] == 'e') {
        i += 4;
        new(&v) mixed(true);
        return true;
      }
      break;
    case 'f':
      if (s[i + 1] == 'a' &&
          s[i + 2] == 'l' &&
          s[i + 3] == 's' &&
          s[i + 4] == 'e') {
        i += 5;
        new(&v) mixed(false);
        return true;
      }
      break;
    case '"': {
      int j = i + 1;
      int slashes = 0;
      while (j < s_len && s[j] != '"') {
        if (s[j] == '\\') {
          slashes++;
          j++;
        }
        j++;
      }
      if (j < s_len) {
        int len = j - i - 1 - slashes;

        string value(len, false);

        i++;
        int l;
        for (l = 0; l < len && i < j; l++) {
          char c = s[i];
          if (c == '\\') {
            i++;
            switch (s[i]) {
              case '"':
              case '\\':
              case '/':
                value[l] = s[i];
                break;
              case 'b':
                value[l] = '\b';
                break;
              case 'f':
                value[l] = '\f';
                break;
              case 'n':
                value[l] = '\n';
                break;
              case 'r':
                value[l] = '\r';
                break;
              case 't':
                value[l] = '\t';
                break;
              case 'u':
                if (isxdigit(s[i + 1]) && isxdigit(s[i + 2]) && isxdigit(s[i + 3]) && isxdigit(s[i + 4])) {
                  int num = 0;
                  for (int t = 0; t < 4; t++) {
                    char c = s[++i];
                    if ('0' <= c && c <= '9') {
                      num = num * 16 + c - '0';
                    } else {
                      c |= 0x20;
                      if ('a' <= c && c <= 'f') {
                        num = num * 16 + c - 'a' + 10;
                      }
                    }
                  }

                  if (0xD7FF < num && num < 0xE000) {
                    if (s[i + 1] == '\\' && s[i + 2] == 'u' &&
                        isxdigit(s[i + 3]) && isxdigit(s[i + 4]) && isxdigit(s[i + 5]) && isxdigit(s[i + 6])) {
                      i += 2;
                      int u = 0;
                      for (int t = 0; t < 4; t++) {
                        char c = s[++i];
                        if ('0' <= c && c <= '9') {
                          u = u * 16 + c - '0';
                        } else {
                          c |= 0x20;
                          if ('a' <= c && c <= 'f') {
                            u = u * 16 + c - 'a' + 10;
                          }
                        }
                      }

                      if (0xD7FF < u && u < 0xE000) {
                        num = (((num & 0x3FF) << 10) | (u & 0x3FF)) + 0x10000;
                      } else {
                        i -= 6;
                        return false;
                      }
                    } else {
                      return false;
                    }
                  }

                  if (num < 128) {
                    value[l] = static_cast<char>(num);
                  } else if (num < 0x800) {
                    value[l++] = static_cast<char>(0xc0 + (num >> 6));
                    value[l] = static_cast<char>(0x80 + (num & 63));
                  } else if (num < 0xffff) {
                    value[l++] = static_cast<char>(0xe0 + (num >> 12));
                    value[l++] = static_cast<char>(0x80 + ((num >> 6) & 63));
                    value[l] = static_cast<char>(0x80 + (num & 63));
                  } else {
                    value[l++] = static_cast<char>(0xf0 + (num >> 18));
                    value[l++] = static_cast<char>(0x80 + ((num >> 12) & 63));
                    value[l++] = static_cast<char>(0x80 + ((num >> 6) & 63));
                    value[l] = static_cast<char>(0x80 + (num & 63));
                  }
                  break;
                }
                /* fallthrough */
              default:
                return false;
            }
            i++;
          } else {
            value[l] = s[i++];
          }
        }
        value.shrink(l);

        new(&v) mixed(value);
        i++;
        return true;
      }
      break;
    }
    case '[': {
      array<mixed> res;
      i++;
      json_skip_blanks(s, i);
      if (s[i] != ']') {
        do {
          mixed value;
          if (!do_json_decode(s, s_len, i, value)) {
            return false;
          }
          res.push_back(value);
          json_skip_blanks(s, i);
        } while (s[i++] == ',');

        if (s[i - 1] != ']') {
          return false;
        }
      } else {
        i++;
      }

      new(&v) mixed(res);
      return true;
    }
    case '{': {
      array<mixed> res;
      i++;
      json_skip_blanks(s, i);
      if (s[i] != '}') {
        do {
          mixed key;
          if (!do_json_decode(s, s_len, i, key) || !key.is_string()) {
            return false;
          }
          json_skip_blanks(s, i);
          if (s[i++] != ':') {
            return false;
          }

          if (!do_json_decode(s, s_len, i, res[key])) {
            return false;
          }
          json_skip_blanks(s, i);
        } while (s[i++] == ',');

        if (s[i - 1] != '}') {
          return false;
        }
      } else {
        i++;
      }

      new(&v) mixed(res);
      return true;
    }
    default: {
      int j = i;
      while (s[j] == '-' || ('0' <= s[j] && s[j] <= '9') || s[j] == 'e' || s[j] == 'E' || s[j] == '+' || s[j] == '.') {
        j++;
      }
      if (j > i) {
        int64_t intval = 0;
        if (php_try_to_int(s + i, j - i, &intval)) {
          i = j;
          new(&v) mixed(intval);
          return true;
        }

        char *end_ptr;
        double floatval = strtod(s + i, &end_ptr);
        if (end_ptr == s + j) {
          i = j;
          new(&v) mixed(floatval);
          return true;
        }
      }
      break;
    }
  }

  return false;
}

static int win_to_utf8_convert[256] = {0x0, 0x1, 0x2, 0x3, 0x4, 0x5, 0x6, 0x7, 0x8, 0x9, 0xa, 0xb, 0xc, 0xd, 0xe, 0xf, 0x10, 0x11, 0x12, 0x13, 0x14, 0x15, 0x16,
                                       0x17, 0x18, 0x19, 0x1a, 0x1b, 0x1c, 0x1d, 0x1e, 0x1f, 0x20, 0x21, 0x22, 0x23, 0x24, 0x25, 0x26, 0x27, 0x28, 0x29, 0x2a,
                                       0x2b, 0x2c, 0x2d, 0x2e, 0x2f, 0x30, 0x31, 0x32, 0x33, 0x34, 0x35, 0x36, 0x37, 0x38, 0x39, 0x3a, 0x3b, 0x3c, 0x3d, 0x3e,
                                       0x3f, 0x40, 0x41, 0x42, 0x43, 0x44, 0x45, 0x46, 0x47, 0x48, 0x49, 0x4a, 0x4b, 0x4c, 0x4d, 0x4e, 0x4f, 0x50, 0x51, 0x52,
                                       0x53, 0x54, 0x55, 0x56, 0x57, 0x58, 0x59, 0x5a, 0x5b, 0x5c, 0x5d, 0x5e, 0x5f, 0x60, 0x61, 0x62, 0x63, 0x64, 0x65, 0x66,
                                       0x67, 0x68, 0x69, 0x6a, 0x6b, 0x6c, 0x6d, 0x6e, 0x6f, 0x70, 0x71, 0x72, 0x73, 0x74, 0x75, 0x76, 0x77, 0x78, 0x79, 0x7a,
                                       0x7b, 0x7c, 0x7d, 0x7e, 0x7f, 0x402, 0x403, 0x201a, 0x453, 0x201e, 0x2026, 0x2020, 0x2021, 0x20ac, 0x2030, 0x409, 0x2039,
                                       0x40a, 0x40c, 0x40b, 0x40f, 0x452, 0x2018, 0x2019, 0x201c, 0x201d, 0x2022, 0x2013, 0x2014, 0x0, 0x2122, 0x459, 0x203a,
                                       0x45a, 0x45c, 0x45b, 0x45f, 0xa0, 0x40e, 0x45e, 0x408, 0xa4, 0x490, 0xa6, 0xa7, 0x401, 0xa9, 0x404, 0xab, 0xac, 0xad,
                                       0xae, 0x407, 0xb0, 0xb1, 0x406, 0x456, 0x491, 0xb5, 0xb6, 0xb7, 0x451, 0x2116, 0x454, 0xbb, 0x458, 0x405, 0x455, 0x457,
                                       0x410, 0x411, 0x412, 0x413, 0x414, 0x415, 0x416, 0x417, 0x418, 0x419, 0x41a, 0x41b, 0x41c, 0x41d, 0x41e, 0x41f, 0x420,
                                       0x421, 0x422, 0x423, 0x424, 0x425, 0x426, 0x427, 0x428, 0x429, 0x42a, 0x42b, 0x42c, 0x42d, 0x42e, 0x42f, 0x430, 0x431,
                                       0x432, 0x433, 0x434, 0x435, 0x436, 0x437, 0x438, 0x439, 0x43a, 0x43b, 0x43c, 0x43d, 0x43e, 0x43f, 0x440, 0x441, 0x442,
                                       0x443, 0x444, 0x445, 0x446, 0x447, 0x448, 0x449, 0x44a, 0x44b, 0x44c, 0x44d, 0x44e, 0x44f};

void write_char_utf8(int c) {
  if (!c) {
    return;
  }
  if (c < 128) {
    static_SB_spare.append_char(c);
    return;
  }
  // 2 bytes(11): 110x xxxx 10xx xxxx
  if (c < 0x800) {
    static_SB_spare.append_char(0xC0 + (c >> 6));
    static_SB_spare.append_char(0x80 + (c & 63));
    return;
  }

  // 3 bytes(16): 1110 xxxx 10xx xxxx 10xx xxxx
  if (c < 0x10000) {
    static_SB_spare.append_char(0xE0 + (c >> 12));
    static_SB_spare.append_char(0x80 + ((c >> 6) & 63));
    static_SB_spare.append_char(0x80 + (c & 63));
    return;
  }

  // 4 bytes(21): 1111 0xxx 10xx xxxx 10xx xxxx 10xx xxxx
  if (c < 0x200000) {
    static_SB_spare.append_char(0xF0 + (c >> 18));
    static_SB_spare.append_char(0x80 + ((c >> 12) & 63));
    static_SB_spare.append_char(0x80 + ((c >> 6) & 63));
    static_SB_spare.append_char(0x80 + (c & 63));
    return;
  }

  // 5 bytes(26): 1111 10xx 10xx xxxx 10xx xxxx 10xx xxxx 10xx xxxx
  if (c < 0x4000000) {
    static_SB_spare.append_char(0xF8 + (c >> 24));
    static_SB_spare.append_char(0x80 + ((c >> 18) & 63));
    static_SB_spare.append_char(0x80 + ((c >> 12) & 63));
    static_SB_spare.append_char(0x80 + ((c >> 6) & 63));
    static_SB_spare.append_char(0x80 + (c & 63));
    return;
  }

  // 6 bytes(31): 1111 110x 10xx xxxx 10xx xxxx 10xx xxxx 10xx xxxx 10xx xxxx
  if (c < 0x80000000) {
    static_SB_spare.append_char(0xFC + (c >> 30));
    static_SB_spare.append_char(0x80 + ((c >> 24) & 63));
    static_SB_spare.append_char(0x80 + ((c >> 18) & 63));
    static_SB_spare.append_char(0x80 + ((c >> 12) & 63));
    static_SB_spare.append_char(0x80 + ((c >> 6) & 63));
    static_SB_spare.append_char(0x80 + (c & 63));
    return;
  }
}

size_t html_encoding(const char *win_str, size_t len, size_t offset) noexcept {
  if (offset + 5 < len && win_str[offset + 1] == 'q' && win_str[offset + 2] == 'u' && win_str[offset + 3] == 'o' && win_str[offset + 4] == 't'
      && win_str[offset + 5] == ';') {
    static_SB_spare.append(R"(\")", 2);
    offset += 5;
  } else if (offset + 4 < len && win_str[offset + 1] == 's' && win_str[offset + 2] == 'h' && win_str[offset + 3] == 'y' && win_str[offset + 4] == ';') {
    offset += 4;
  } else if (offset + 6 < len && win_str[offset + 1] == 'n' && win_str[offset + 2] == 'd' && win_str[offset + 3] == 'a' && win_str[offset + 4] == 's'
             && win_str[offset + 5] == 'h' && win_str[offset + 6] == ';') {
    static_SB_spare.append("–", 3);
    offset += 6;
  } else if (offset + 4 < len && win_str[offset + 1] == 'a' && win_str[offset + 2] == 'm' && win_str[offset + 3] == 'p' && win_str[offset + 4] == ';') {
    static_SB_spare.append_char('&');
    offset += 4;
  } else if (offset + 3 < len && win_str[offset + 1] == 'l' && win_str[offset + 2] == 't' && win_str[offset + 3] == ';') {
    static_SB_spare.append_char('<');
    offset += 3;
  } else if (offset + 3 < len && win_str[offset + 1] == 'g' && win_str[offset + 2] == 't' && win_str[offset + 3] == ';') {
    static_SB_spare.append_char('>');
    offset += 3;
  } else {
    static_SB_spare.append_char(win_str[offset]);
  }
  return offset;
}
} // namespace

string json_string_win_to_utf([[maybe_unused]] const char *win_str) noexcept {
  static_SB_spare.clean();
  size_t len = static_SB.size();

  int state = 0;
  int save_pos = -1;
  int cur_num = 0;

  for (size_t i = 0; i < len; ++i) {
    if (state == 0 && win_str[i] == '&') {
      size_t new_offset = html_encoding(win_str, len, i);
      if (i == new_offset) {
        cur_num = 0;
        save_pos = static_SB_spare.size();
        state = 1;
      } else {
        i = new_offset;
        state = 0;
        continue;
      }
    } else if (state == 1 && win_str[i] == '#') {
      state = 2;
    } else if (state == 2 && win_str[i] >= '0' && win_str[i] <= '9') {
      cur_num = win_str[i] - '0' + cur_num * 10;
    } else if (state == 2 && win_str[i] == ';') {
      state = 3;
    } else if (win_str[i] == '<') {
      bool obr_tag = i + 3 < len && win_str[i + 1] == 'b' && win_str[i + 2] == 'r' && win_str[i + 3] == '>';
      if (obr_tag
          || (i + 5 < len && win_str[i + 1] == 'b' && win_str[i + 2] == 'r' && win_str[i + 3] == '\\' && win_str[i + 4] == '/' && win_str[i + 5] == '>')) {
        static_SB_spare.append_char(' ');
        i += obr_tag ? 4 : 6;
        state = 0;
      }
    } else if (win_str[i] == '\t') {
//    } else if (win_str[i] == '\\' && i + 1 < len && win_str[i + 1] == 't') {
      static_SB_spare.append("\\n", 2);
      i++;
      state = 0;
      continue;
    } else if (win_str[i] == '\r') {
      static_SB_spare.append("\\r", 2);
      state = 0;
      continue;
    } else {
      state = 0;
    }

    if (state == 3 && 0xd800 <= cur_num && cur_num <= 0xdfff) {
      cur_num = 32;
    }

    if (state == 3 && (cur_num == 13 || (cur_num >= 32 && cur_num != 60 && cur_num != 62 && cur_num != 8232 && cur_num != 8233 && cur_num < 0x80000000))) {
      static_SB_spare.set_pos(save_pos - 1);
      if (cur_num == 13) {
        static_SB_spare.append(R"(\\r)", 3);
      } else if (cur_num == 33 || cur_num == 36 || cur_num == 39) {
        static_SB_spare.append_char(cur_num);
      } else if (cur_num == 34) {
        static_SB_spare.append(R"(\")", 2);
      } else if (cur_num == 92) {
        static_SB_spare.append(R"(\\)", 2);
      } else {
        write_char_utf8(cur_num);
      }
    } else {
      write_char_utf8(win_to_utf8_convert[static_cast<unsigned char>(win_str[i])]);
    }
    if (state == 3) {
      state = 0;
    }
  }

  return static_SB_spare.str();
}

mixed f$json_decode(const string &v, bool assoc) noexcept {
  // TODO It was a warning before (in case if assoc is false), but then it was disabled, should we enable it again?
  static_cast<void>(assoc);

  mixed result;
  int i = 0;
  if (do_json_decode(v.c_str(), v.size(), i, result)) {
    json_skip_blanks(v.c_str(), i);
    if (i == static_cast<int>(v.size())) {
      return result;
    }
  }

  return mixed();
}
