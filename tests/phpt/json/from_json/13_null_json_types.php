@ok
<?php
require_once 'kphp_tester_include.php';

class A {}

class BasicTypes {
  public int $int_f;
  public bool $bool_f;
  public string $string_f;
  public float $float_f;
  public A $a_f;
  /** @var int[] */
  public $array_f;
}

function test_null_json_types() {
  $obj = from_json("{}", "BasicTypes");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["int_f" => 0, "bool_f" => false, "string_f" => "", "float_f" => 0.0, "a_f" => null, "array_f" => []];
  #endif
  var_dump($arr);

  $obj = from_json("{\"int_f\":null,\"bool_f\":null,\"string_f\":null,\"float_f\":null,\"a_f\":null,\"array_f\":null}", "BasicTypes");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["int_f" => 0, "bool_f" => false, "string_f" => "", "float_f" => 0.0, "a_f" => null, "array_f" => []];
  #endif
  var_dump($arr);

  $obj = from_json("{\"int_f\":123,\"bool_f\":true,\"string_f\":\"foo\",\"float_f\":123.45,\"a_f\":{},\"array_f\":[11, 44]}", "BasicTypes");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["int_f" => 123, "bool_f" => true, "string_f" => "foo", "float_f" => 123.45, "a_f" => [], "array_f" => [11, 44]];
  #endif
  var_dump($arr);
}

test_null_json_types();
