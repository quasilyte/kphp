@ok
<?php
require_once 'kphp_tester_include.php';

class MixedArrays {
  /** @var mixed */
  public $empty_vec;
  /** @var mixed */
  public $int_vec;
  /** @var mixed */
  public $bool_vec;
  /** @var mixed */
  public $string_vec;
  /** @var mixed */
  public $float_vec;
}

class MixedArrayOfArrays {
  /** @var mixed */
  public $arr;
}

function test_array_of_null_mixed() {
  $json = "{}";
  $obj = from_json($json, "MixedArrays");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["empty_vec" => null, "int_vec" => null, "bool_vec" => null,
   "string_vec" => null, "float_vec" => null];
  #endif
  var_dump($arr);

  $json = "{\"empty_vec\":null,\"int_vec\":null,\"bool_vec\":null," .
    "\"string_vec\":null,\"float_vec\":null}";
  $obj = from_json($json, "MixedArrays");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["empty_vec" => null, "int_vec" => null, "bool_vec" => null,
   "string_vec" => null, "float_vec" => null];
  #endif
  var_dump($arr);
}

function test_array_of_mixed() {
  $json = "{\"empty_vec\":[],\"int_vec\":{\"a\":11, \"b\":22, \"c\":33},\"bool_vec\":[true,false]," .
    "\"string_vec\":{\"a\":\"foo\", \"b\":\"bar\"},\"float_vec\":[56.78]}";
  $obj = from_json($json, "MixedArrays");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["empty_vec" => [], "int_vec" => ["a" => 11, "b" => 22, "c" => 33], "bool_vec" => [true, false],
   "string_vec" => ["a" => "foo", "b" => "bar"], "float_vec" => [56.78]];
  #endif
  var_dump($arr);
}

function test_array_of_arrays_mixed() {
  $json = "{\"arr\":[[11,22],[33,44]]}";
  $obj = from_json($json, "MixedArrayOfArrays");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["arr" => [[11, 22], [33, 44]]];
  #endif
  var_dump($arr);
}

test_array_of_null_mixed();
test_array_of_mixed();
test_array_of_arrays_mixed();
