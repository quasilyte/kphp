@kphp_runtime_should_not_warn
<?php
require_once 'kphp_tester_include.php';

class SimpleB {
  public int $b_int;
}

class SimpleA {
  public int $a_int;
  public SimpleB $a_b_instance;
}

function test_null_object() {
  /** @var SimpleA[] */
  $dummy_arr = [];
  $null_a = $dummy_arr[0];

  $json = to_json($null_a);
  $restored_a = from_json($json, "SimpleA");

  $dump = to_array_debug($restored_a);
  #ifndef KPHP
  $dump = [];
  #endif
  var_dump($dump);
}

function test_null_object_inner() {
  $origin_a = new SimpleA;
  $origin_a->a_int = 99;

  $json = to_json($origin_a);
  $restored_a = from_json($json, "SimpleA");

  $dump = to_array_debug($restored_a);
  #ifndef KPHP
  $dump = ["a_int" => 99, "a_b_instance" => null];
  #endif
  var_dump($dump);
}

test_null_object();
test_null_object_inner();
