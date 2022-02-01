// Compiler for PHP (aka KPHP)
// Copyright (c) 2020 LLC «V Kontakte»
// Distributed under the GPL v3 License, see LICENSE.notice.txt

#pragma once

#include <rapidjson/prettywriter.h>

#include "runtime/kphp_core.h"

class ToJsonVisitor {
public:
  ToJsonVisitor(rapidjson::Writer<rapidjson::StringBuffer> &writer, bool with_class_names) noexcept
    : with_class_names_(with_class_names)
    , writer_(writer) {}

  template<typename T>
  void operator()(std::string_view key, const T &value) {
    process_impl(key, value);
  }

private:
  template<class T>
  void process_impl(std::string_view key, const T &value) {
    writer_.Key(key.data(), key.size());
    add_value(value);
  }

  template<typename T>
  void process_impl(std::string_view key, const Optional<T> &value) {
    auto process_impl_lambda = [this, key](const auto &val) { return this->process_impl(key, val); };
    call_fun_on_optional_value(process_impl_lambda, value);
  }

  template<class I>
  void process_impl(std::string_view key, const class_instance<I> &instance) {
    writer_.Key(key.data(), key.size());
    to_json_impl(instance, with_class_names_, writer_);
  }

  void add_value(const string &value) {
    writer_.String(value.c_str(), value.size());
  }

  void add_value(std::int64_t value) {
    writer_.Int64(value);
  }

  void add_value(bool value) {
    writer_.Bool(value);
  }

  void add_value(double value) {
    writer_.Double(value);
  }

  void add_null_value() {
    writer_.Null();
  }

  void add_value(const mixed &value) {
    switch(value.get_type()) {
      case mixed::type::NUL :
        add_null_value();
        break;
      case mixed::type::BOOLEAN:
        add_value(value.as_bool());
        break;
      case mixed::type::INTEGER:
        add_value(value.as_int());
        break;
      case mixed::type::FLOAT:
        add_value(value.as_double());
        break;
      case mixed::type::STRING:
        add_value(value.as_string());
        break;
      case mixed::type::ARRAY:
        add_value(value.as_array());
        break;
    }
  }

  bool with_class_names_{false};
  rapidjson::Writer<rapidjson::StringBuffer> &writer_;
};

template<class T>
void to_json_impl(const class_instance<T> &klass, bool with_class_names, rapidjson::Writer<rapidjson::StringBuffer> &writer) {
  if (klass.is_null()) {
    writer.Null();
    return;
  }

  writer.StartObject();

  ToJsonVisitor visitor{writer, with_class_names};
  if constexpr (!std::is_empty_v<T>) {
    klass.get()->accept(visitor);
  }

  if (with_class_names) {
    visitor("__class_name", string{klass.get_class()});
  }

  writer.EndObject();
}

template<class T>
string f$to_json(const class_instance<T> &klass, bool with_class_names = false) {
  rapidjson::StringBuffer buffer;
  rapidjson::Writer<rapidjson::StringBuffer> writer{buffer};
  to_json_impl(klass, with_class_names, writer);
  php_assert(writer.IsComplete());
  return {buffer.GetString(), static_cast<std::uint32_t>(buffer.GetSize())};
}
