// Compiler for PHP (aka KPHP)
// Copyright (c) 2021 LLC «V Kontakte»
// Distributed under the GPL v3 License, see LICENSE.notice.txt

#include "runtime/kphp_core.h"

// Don't move this destructor to the headers, it spoils addr2line traces
string::~string() noexcept {
  destroy();
}

string string_concat2(const string &s1, const string &s2) noexcept {
  return string(s1.size() + s2.size(), true).append_unsafe(s1).append_unsafe(s2).finish_append();
}

string string_concat3(const string &s1, const string &s2, const string &s3) noexcept {
  return string(s1.size() + s2.size() + s3.size(), true).append_unsafe(s1).append_unsafe(s2).append_unsafe(s3).finish_append();
}

string string_concat4(const string &s1, const string &s2, const string &s3, const string &s4) noexcept {
  return string(s1.size() + s2.size() + s3.size() + s4.size(), true).append_unsafe(s1).append_unsafe(s2).append_unsafe(s3).append_unsafe(s4).finish_append();
}

string string_concat5(const string &s1, const string &s2, const string &s3, const string &s4, const string &s5) noexcept {
  return string(s1.size() + s2.size() + s3.size() + s4.size() + s5.size(), true).
    append_unsafe(s1).append_unsafe(s2).append_unsafe(s3).append_unsafe(s4).append_unsafe(s5).
    finish_append();
}

string string_concat2si(const string &x, int64_t y) noexcept {
  return string(x.size() + max_string_size(y), true).append_unsafe(x).append_unsafe(y).finish_append();
}
