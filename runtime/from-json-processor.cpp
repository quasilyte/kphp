// Compiler for PHP (aka KPHP)
// Copyright (c) 2020 LLC «V Kontakte»
// Distributed under the GPL v3 License, see LICENSE.notice.txt

#include "runtime/from-json-processor.h"

using namespace rapidjson;

std::string_view json_type_string(Type type) noexcept {
  switch (type) {
    case kNullType: return "null";
    case kFalseType:
    case kTrueType: return "boolean";
    case kObjectType: return "object";
    case kArrayType: return "array";
    case kStringType: return "string";
    case kNumberType: return "number";
    default: return "unknown";
  }
}
