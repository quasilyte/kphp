#pragma once

#include "compiler/code-gen/gen-out-style.h"
#include "compiler/data/function-data.h"
#include "compiler/data/var-data.h"
#include "compiler/inferring/type-data.h"

struct LabelName {
  int label_id;
  explicit LabelName(int label_id) : label_id(label_id) {}

  void compile(CodeGenerator &W) const {
    W << "label" << int_to_str(label_id);
  }
};

struct TypeNameInsideMacro {
  const TypeData *type;
  explicit TypeNameInsideMacro(const TypeData *type) : type(type) { }

  void compile(CodeGenerator &W) const {
    string s = type_out(type);
    while (s.find(',') != string::npos) {
      s = s.replace(s.find(','), 1, " COMMA ");   // такое есть у tuple'ов
    }
    W << s;
  }
};

struct TypeName {
  const TypeData *type;
  gen_out_style style;
  explicit TypeName(const TypeData *type, gen_out_style style = gen_out_style::cpp) :
    type(type),
    style(style) {
  }

  void compile(CodeGenerator &W) const {
    W << type_out(type, style == gen_out_style::cpp);
  }
};

struct FunctionName {
  FunctionPtr function;
  explicit FunctionName(FunctionPtr function) :
    function(function) {
  }

  void compile(CodeGenerator &W) const {
    W << "f$";
    if (W.get_context().use_safe_integer_arithmetic && function->name == "intval") {
      W << "safe_intval";
    } else {
      W << function->name;
    }
  }
};

struct FunctionCallFlag {
  FunctionPtr function;
  inline FunctionCallFlag(FunctionPtr function) :
    function(function) {
  }

  // will be removed in future
  inline void compile(CodeGenerator &W) const {
    W << "v$" + function->name << "$called";
  }
};

struct FunctionForkName {
  FunctionPtr function;
  inline FunctionForkName(FunctionPtr function) : function(function) {}

  void compile(CodeGenerator &W) const {
    W << "f$fork$" << function->name;
  }
};

struct FunctionClassName {
  FunctionPtr function;
  explicit FunctionClassName(FunctionPtr function) :
    function(function) {
  }

  void compile(CodeGenerator &W) const {
    W << "c$" << function->name;
  }
};

struct VarName {
  VarPtr var;
  explicit VarName(VarPtr var) : var(var) {}

  void compile(CodeGenerator &W) const {
    if (var->is_function_static_var()) {
      W << FunctionName(var->holder_func) << "$";
    }

    W << "v$" << var->name;
  }
};

struct GlobalVarsResetFuncName {
  explicit GlobalVarsResetFuncName(FunctionPtr main_func, int part = -1) :
    main_func_(main_func),
    part_(part) {}

  void compile(CodeGenerator &W) const {
    W << FunctionName(main_func_) << "$global_vars_reset";
    if (part_ >= 0) {
      W << std::to_string(part_);
    }
    W << "()";
  }

private:
  const FunctionPtr main_func_;
  const int part_{-1};
};
