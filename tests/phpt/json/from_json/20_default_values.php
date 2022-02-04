@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public string $foo = "default_foo";
  public string $bar = "default_bar";
}

function test_default_first_field() {
  $json = "{\"bar\":\"baz\"}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["foo" => "default_foo", "bar" => "baz"];
  #endif
  var_dump($dump);
}

function test_default_second_field() {
  $json = "{\"foo\":\"baz\"}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["foo" => "baz", "bar" => "default_bar"];
  #endif
  var_dump($dump);
}

function test_default_both_fields() {
  $json = "{}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["foo" => "default_foo", "bar" => "default_bar"];
  #endif
  var_dump($dump);
}

test_default_first_field();
test_default_second_field();
test_default_both_fields();
