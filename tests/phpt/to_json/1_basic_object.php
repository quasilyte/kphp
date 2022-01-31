@ok
<?php
require_once 'kphp_tester_include.php';

class EmptyClass {}

class SimpleB {
  public int $b_int = 42;
}

class SimpleA {
  public int $a_int = 42;
  public SimpleB $a_b_instance;
}

class BasicTypes {
  public int $int_positive_f = 123;
  public int $int_negative_f = -42;
  public bool $bool_true_f = true;
  public bool $bool_false_f = false;
  public string $string_f = "foo";
  public float $float_positive_f = 123.45;
  public float $float_negative_f = -98.76;
}

function test_empty_class() {
  $json = to_json(new EmptyClass);
  #ifndef KPHP
  $json = "{}";
  #endif
  echo $json;
}

function test_null_object() {
  /** @var SimpleA[] */
  $dummy_arr = [];
  $null_a = $dummy_arr[0];
  $json = to_json($null_a);
  #ifndef KPHP
  $json = "null";
  #endif
  echo $json;
}

function test_nested_null_object() {
  $json = to_json(new SimpleA);
  #ifndef KPHP
  $json = "{\"a_int\":42,\"a_b_instance\":null}";
  #endif
  echo $json;
}

function test_basic_types() {
  $json = to_json(new BasicTypes);
  #ifndef KPHP
  $json = "{\"int_positive_f\":123,\"int_negative_f\":-42,\"bool_true_f\":true,\"bool_false_f\":false," .
  "\"string_f\":\"foo\",\"float_positive_f\":123.45,\"float_negative_f\":-98.76}";
  #endif
  echo $json;
}

test_empty_class();
test_null_object();
test_nested_null_object();
test_basic_types();
