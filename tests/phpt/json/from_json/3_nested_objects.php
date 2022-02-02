@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public int $a_int;
}

class B {
  public int $b_int;
  public A $b_a;
}

class C extends A {
  public int $c_int;
}

function test_nested_types() {
  $obj = from_json("{\"b_int\":99,\"b_a\":{\"a_int\":88}}", "B");
  #ifndef KPHP
  $obj = new B;
  $obj->b_int = 99;
  $obj->b_a = new A;
  $obj->b_a->a_int = 88;
  #endif
  var_dump(to_array_debug($obj));
}

function test_inheritance() {
  $obj = from_json("{\"c_int\":77,\"a_int\":44}", "C");
  #ifndef KPHP
  $obj = new C;
  $obj->c_int = 77;
  $obj->a_int = 44;
  #endif
  var_dump(to_array_debug($obj));
}

test_nested_types();
test_inheritance();
