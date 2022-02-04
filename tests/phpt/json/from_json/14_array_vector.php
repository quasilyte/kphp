@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public int $id;
}

class ArrayVector {
  public $empty_vec = [];
  /** @var int[] */
  public $int_vec;
  /** @var bool[] */
  public $bool_vec;
  /** @var string[] */
  public $string_vec;
  /** @var float[] */
  public $float_vec;
  /** @var A[] */
  public $obj_vec;
}

class ArrayNullObject {
  /** @var A[] */
  public $vec = [];
}

function test_array_vector() {
  $json = "{\"empty_vec\":[],\"int_vec\":[11,22,33],\"bool_vec\":[true,false]," .
   "\"string_vec\":[\"foo\",\"bar\"],\"float_vec\":[56.78],\"obj_vec\":[{\"id\":1},{\"id\":2}]}";
  $obj = from_json($json, "ArrayVector");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["empty_vec" => [], "int_vec" => [11,22,33], "bool_vec" => [true, false],
   "string_vec" => ["foo", "bar"], "float_vec" => [56.78], "obj_vec" => [["id" => 1], ["id" => 2]]];
  #endif
  var_dump($arr);
}

function test_array_vector_of_nulls() {
  $json = "{\"empty_vec\":[],\"int_vec\":[null],\"bool_vec\":[null,null]," .
   "\"string_vec\":[null],\"float_vec\":[null, null],\"obj_vec\":[null, null]}";
  $obj = from_json($json, "ArrayVector");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["empty_vec" => [], "int_vec" => [0], "bool_vec" => [false, false],
   "string_vec" => [""], "float_vec" => [0.0, 0.0], "obj_vec" => [null, null]];
  #endif
  var_dump($arr);
}

function test_array_vector_null_object() {
  $obj = from_json("{\"vec\":[{\"id\":1},null]}", "ArrayNullObject");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["vec" => [["id" => 1], null]];
  #endif
  var_dump($arr);
}

test_array_vector();
test_array_vector_of_nulls();
test_array_vector_null_object();
