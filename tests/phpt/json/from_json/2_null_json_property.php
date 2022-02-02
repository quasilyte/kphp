@ok
<?php
require_once 'kphp_tester_include.php';

class SimpleB {
  public int $b_int;
}

class SimpleA {
  public int $a_int;
  public SimpleB $a_b_instance;
}

function test_null_property() {
  $obj = from_json("{\"b_int\":null}", "SimpleB");
  #ifndef KPHP
  $obj = new Simpleb;
  $obj->b_int = 0;
  #endif
  var_dump(to_array_debug($obj));
}

function test_empty_object() {
  $obj = from_json("{}", "SimpleB");
  #ifndef KPHP
  $obj = new Simpleb;
  $obj->b_int = 0;
  #endif
  var_dump(to_array_debug($obj));
}

function test_null_property_2() {
  $obj = from_json("{\"a_int\":99,\"a_b_instance\":{\"b_int\":null}}", "SimpleA");
  #ifndef KPHP
  $obj = new SimpleA;
  $obj->a_int = 99;
  $obj->a_b_instance = new SimpleB;
  $obj->a_b_instance->b_int = 0;
  #endif
  var_dump(to_array_debug($obj));
}

function test_null_object() {
  $obj = from_json("{\"a_int\":99,\"a_b_instance\":null}", "SimpleA");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["a_int" => 99, "a_b_instance" => null];
  #endif
  var_dump($arr);
}

function test_absent_object() {
  $obj = from_json("{\"a_int\":99}", "SimpleA");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["a_int" => 99, "a_b_instance" => null];
  #endif
  var_dump($arr);
}

test_null_property();
test_empty_object();
test_null_property_2();
test_null_object();
test_absent_object();
