@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public int $id;
}

class ArrayMap {
  public $empty_map = [];
  /** @var int[] */
  public $int_map;
  /** @var bool[] */
  public $bool_map;
  /** @var string[] */
  public $string_map;
  /** @var float[] */
  public $float_map;
  /** @var A[] */
  public $obj_map = [];
}

function test_array_map() {
  $json = "{\"empty_map\":[],\"int_map\":{\"0\":11,\"99\":22,\"88\":33},\"bool_map\":{\"a\":true,\"b\":false}," .
   "\"string_map\":{\"2\":\"foo\",\"c\":\"bar\"},\"float_map\":{\"20\":56.78},\"obj_map\":{\"a\":{\"id\":1},\"b\":{\"id\":2}}}";
  $obj = from_json($json, "ArrayMap");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["empty_map" => [], "int_map" => ["0" => 11, "99" => 22, "88" => 33], "bool_map" => ["a" => true, "b" => false],
   "string_map" => ["2" => "foo", "c" => "bar"], "float_map" => ["20" => 56.78], "obj_map" => ["a" => ["id" => 1], "b" => ["id" => 2]]];
  #endif
  var_dump($arr);
}

function test_array_map_of_nulls() {
  $json = "{\"empty_map\":[],\"int_map\":{\"0\":null,\"99\":22,\"88\":null},\"bool_map\":{\"a\":null,\"b\":null}," .
   "\"string_map\":{\"2\":null,\"c\":\"bar\"},\"float_map\":{\"20\":null},\"obj_map\":{\"a\":null,\"b\":{\"id\":null}}}";
  $obj = from_json($json, "ArrayMap");
  $arr = to_array_debug($obj);
  #ifndef KPHP
  $arr = ["empty_map" => [], "int_map" => ["0" => 0, "99" => 22, "88" => 0], "bool_map" => ["a" => false, "b" => false],
   "string_map" => ["2" => "", "c" => "bar"], "float_map" => ["20" => 0.0], "obj_map" => ["a" => null, "b" => ["id" => 0]]];
  #endif
  var_dump($arr);
}

test_array_map();
test_array_map_of_nulls();
