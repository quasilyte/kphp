@ok
<?php
require_once 'kphp_tester_include.php';

class C {
  public int $idx;
}

class B {
  /** @var C[] */
  public $arr_of_c;
}

class A {
  /** @var B[] */
  public $arr_of_b;
}

class D {
  /** @var C[][] */
  public $arr;
}

function test_array_nested() {
  $json = "{\"arr_of_b\":{\"a\":{\"arr_of_c\":[{\"idx\":11},{\"idx\":22}]},\"b\":{\"arr_of_c\":[{\"idx\":33},{\"idx\":44}]}}}";
  $obj = from_json($json, "A");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["arr_of_b" => ["a" => ["arr_of_c" => [["idx" => 11], ["idx" => 22]]], "b" => ["arr_of_c" => [["idx" => 33], ["idx" => 44]]]]];
  #endif
  var_dump($arr);
}

function test_2_dimensional_array() {
  $json = "{\"arr\":[[{\"idx\":1},{\"idx\":2}],[{\"idx\":3},{\"idx\":4}]]}";
  $obj = from_json($json, "D");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["arr" => [[["idx" => 1], ["idx" => 2]], [["idx" => 3], ["idx" => 4]]]];
  #endif
  var_dump($arr);
}

test_array_nested();
test_2_dimensional_array();
