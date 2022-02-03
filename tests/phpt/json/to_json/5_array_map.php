@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public int $id = 0;
  function __construct($id) {
    $this->id = $id;
  }
}

class ArrayMap {
  public $empty_map = [];
  public $int_map = [11, 99 => 22, 88 => 33];
  public $bool_map = ["a" => true, "b" => false];
  public $string_map = [2 => "foo", "c" => "bar"];
  public $float_map = [20 => 56.78];
  public $obj_map = [];

  function __construct() {
    $this->obj_map = ["a" => new A(1), "b" => new A(2)];
  }
}

class ArrayNullObject {
  public $map = [];

  function __construct() {
    /** @var A[] */
    $dummy_arr = [];
    $null_a = $dummy_arr[0];
    $this->map = ["a" => new A(1), "b" => $null_a];
  }
}

function test_array_map() {
  $json = to_json(new ArrayMap);
  #ifndef KPHP
  $json = "{\"empty_map\":[],\"int_map\":{\"0\":11,\"99\":22,\"88\":33},\"bool_map\":{\"a\":true,\"b\":false}," .
   "\"string_map\":{\"2\":\"foo\",\"c\":\"bar\"},\"float_map\":{\"20\":56.78},\"obj_map\":{\"a\":{\"id\":1},\"b\":{\"id\":2}}}";
  #endif
  echo $json;
}

function test_array_map_null_object() {
  $json = to_json(new ArrayNullObject);
  #ifndef KPHP
  $json = "{\"map\":{\"a\":{\"id\":1},\"b\":null}}";
  #endif
  echo $json;
}

test_array_map();
test_array_map_null_object();
