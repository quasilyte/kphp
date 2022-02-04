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
  $obj = new B;
  $obj->b_int = 99;
  $obj->b_a = new A;
  $obj->b_a->a_int = 88;

  $json = to_json($obj);
  $restored_obj = from_json($json, "B");

  #ifndef KPHP
  $restored_obj = $obj;
  #endif

  var_dump(to_array_debug($restored_obj));
}

function test_inheritance() {
  $obj = new C;
  $obj->c_int = 77;
  $obj->a_int = 44;

  $json = to_json($obj);
  $restored_obj = from_json($json, "C");

  #ifndef KPHP
  $restored_obj = $obj;
  #endif

  var_dump(to_array_debug($restored_obj));
}

test_nested_types();
test_inheritance();
