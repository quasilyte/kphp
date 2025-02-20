// Compiler for PHP (aka KPHP)
// Copyright (c) 2021 LLC «V Kontakte»
// Distributed under the GPL v3 License, see LICENSE.notice.txt

#pragma once

#include "compiler/data/data_ptr.h"
#include "compiler/function-pass.h"

class ConvertInvokeToFuncCallPass final : public FunctionPassBase {
public:
  std::string get_description() override {
    return "Convert invoke to func call";
  }

  bool check_function(FunctionPtr function) const final {
    return !function->is_extern();
  }

  VertexPtr on_exit_vertex(VertexPtr root) final;

  VertexPtr on_clone(VertexAdaptor<op_clone> v_clone);
  VertexPtr on_invoke_call(VertexAdaptor<op_invoke_call> v_invoke_call);
  VertexPtr on_callback_of_builtin(VertexAdaptor<op_callback_of_builtin> v_callback);
};
