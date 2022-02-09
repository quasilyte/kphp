// Compiler for PHP (aka KPHP)
// Copyright (c) 2020 LLC «V Kontakte»
// Distributed under the GPL v3 License, see LICENSE.notice.txt

#pragma once

#include <string_view>
#include <stdexcept>

#include <rapidjson/document.h>
#include <rapidjson/error/en.h>

#include "runtime/kphp_core.h"

std::string_view json_type_string(rapidjson::Type type) noexcept;

class FromJsonVisitor {
public:
  explicit FromJsonVisitor(const rapidjson::Value &json) noexcept
    : json_(json)
    , current_it_(json_.MemberBegin()) {}

  template<typename T>
  void operator()(std::string_view key, T &value) noexcept {
    if (!error_.empty()) {
      return;
    }

    if (const auto *json_value = find_value(key); json_value && !json_value->IsNull()) {
      do_set(key, value, *json_value);
    }
  }

  bool has_error() const noexcept { return !error_.empty(); }
  const string &get_error() const noexcept { return error_; }

private:
  const rapidjson::Value *find_value_caching(std::string_view instance_key) noexcept {
    // this method will succeed if order of keys in json object will match order of variables in kphp instance,
    // so we can avoid linear search;
    // the orders above always match for json produced ourselves by to_json()
    if (current_it_ == json_.MemberEnd()) {
      return nullptr;
    }
    std::string_view json_key{current_it_->name.GetString(), current_it_->name.GetStringLength()};
    return json_key == instance_key ? &(current_it_++)->value : nullptr;
  }

  const rapidjson::Value *find_value_linear(std::string_view instance_key) noexcept {
    // fallback for linear search just in case
    auto value_it = json_.FindMember(instance_key.data());
    return value_it == json_.MemberEnd() ? nullptr : &value_it->value;
  }

  const rapidjson::Value *find_value(std::string_view key) noexcept {
    return find_value_caching(key) ?: find_value_linear(key);
  }

  void store_error_message_for(std::string_view key, const rapidjson::Value &json) noexcept {
     error_.assign("unexpected type ");
     error_.append(json_type_string(json.GetType()).data());
     error_.append(" for variable '");
     error_.append(key.data());
     error_.append("'");
  }

  template<typename T>
  void do_set(std::string_view key, T &value, const rapidjson::Value &json) noexcept {
    if (!json.Is<T>()) {
      store_error_message_for(key, json);
      return;
    }
    value = json.Get<T>();
  }

  void do_set(std::string_view key, string &value, const rapidjson::Value &json) noexcept {
    if (!json.IsString()) {
      store_error_message_for(key, json);
      return;
    }
    value.assign(json.GetString(), json.GetStringLength());
  }

  template<typename T>
  void do_set(std::string_view key, Optional<T> &value, const rapidjson::Value &json) noexcept {
    do_set(key, value.ref(), json);
  }

  template<typename I>
  void do_set(std::string_view key, class_instance<I> &klass, const rapidjson::Value &json) noexcept;

  // just don't fail compilation with empty untyped arrays
  void do_set(std::string_view /*key*/, array<Unknown> &/*array*/, const rapidjson::Value &/*json*/) noexcept {}

  template<typename T>
  void do_set_vector(std::string_view key, array<T> &array, const rapidjson::Value &json) noexcept {
    array.reserve(json.Size(), 0, true);

    for (const auto &json_elem : json.GetArray()) {
      auto &elem = array.emplace_back(); // create value anyway despite that json value may be null
      if (!json_elem.IsNull()) {
        do_set(key, elem, json_elem);
      }
    }
  }

  template<typename T>
  void do_set_map(std::string_view key, array<T> &array, const rapidjson::Value &json) noexcept {
    array.reserve(0, json.MemberCount(), false);

    for (const auto &[json_key, json_elem] : json.GetObject()) {
      const auto json_key_str = string{json_key.GetString(), json_key.GetStringLength()};
      auto &elem = array[json_key_str]; // create value anyway despite that json value may be null
      if (!json_elem.IsNull()) {
        do_set(key, elem, json_elem);
      }
    }
  }

  template<typename T>
  void do_set(std::string_view key, array<T> &array, const rapidjson::Value &json) noexcept {
    if (json.IsObject()) {
      do_set_map(key, array, json);
    } else if (json.IsArray()) {
      do_set_vector(key, array, json);
    } else {
      store_error_message_for(key, json);
    }
  }

  void do_set(std::string_view key, mixed &value, const rapidjson::Value &json) noexcept {
    if (json.IsNumber()) {
      do_set_number(value, json);
    } else if (json.IsBool()) {
      value = json.GetBool();
    } else if (json.IsString()) {
      value.assign(json.GetString(), json.GetStringLength());
    } else if (json.IsArray() || json.IsObject()) {
      array<mixed> array;
      do_set(key, array, json);
      value = std::move(array);
    } else {
      store_error_message_for(key, json);
    }
  }

  void do_set_number(mixed &value, const rapidjson::Value &json) noexcept {
    if (json.IsInt64()) {
      value = json.GetInt64();
    } else {
      value = json.GetDouble();
    }
  }

  string error_;
  const rapidjson::Value &json_;
  rapidjson::Value::ConstMemberIterator current_it_;
};

template<typename ClassName>
ClassName from_json_impl(const rapidjson::Value &json) noexcept {
  ClassName instance;
  if constexpr (std::is_empty_v<typename ClassName::ClassType>) {
    instance.empty_alloc();
  } else {
    instance.alloc();
    FromJsonVisitor visitor{json};
    instance.get()->accept(visitor);
    if (visitor.has_error()) {
      php_warning("from_json() error: %s", visitor.get_error().c_str());
      return {};
    }
  }
  return instance;
}

template<typename I>
void FromJsonVisitor::do_set(std::string_view key, class_instance<I> &klass, const rapidjson::Value &json) noexcept {
  if (!json.IsObject()) {
    store_error_message_for(key, json);
    return;
  }
  klass = from_json_impl<class_instance<I>>(json);
}

template<typename ClassName>
ClassName f$from_json(const string &json_string, const string &/*class_mame*/) noexcept {
  rapidjson::Document json;
  json.Parse<rapidjson::ParseFlag::kParseNanAndInfFlag>(json_string.c_str(), json_string.size());

  if (json.HasParseError()) {
    php_warning("from_json() error: invalid json string at offset %zu: %s", json.GetErrorOffset(), GetParseError_En(json.GetParseError()));
    return {};
  }
  if (json.IsNull()) {
    return {};
  }
  if (!json.IsObject()) {
    php_warning("from_json() error: root element must be an object type, got %s", json_type_string(json.GetType()).data());
    return {};
  }

  try {
    return from_json_impl<ClassName>(json.GetObject());
  } catch (const std::exception &ex) {
    php_warning("from_json() unexpected error: %s", ex.what());
    return {};
  }
}
