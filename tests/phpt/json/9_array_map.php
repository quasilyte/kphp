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

  function init() {
    $this->int_map = ["0" => 11, "99" => 22, "88" => 33];
    $this->bool_map = ["a" => true, "b" => false];
    $this->string_map = ["2" => "foo", "c" => "bar"];
    $this->float_map = ["20" => 56.78];
    $a1 = new A;
    $a1->id = 1;
    $a2 = new A;
    $a2->id = 2;
    $this->obj_map = ["a" => $a1, "b" => $a2];
  }
}

class ArrayNullObject {
  /** @var A[] */
  public $vec = [];
}

function test_array_map() {
  $obj = new ArrayMap;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "ArrayMap");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["empty_map" => [], "int_map" => ["0" => 11, "99" => 22, "88" => 33], "bool_map" => ["a" => true, "b" => false],
   "string_map" => ["2" => "foo", "c" => "bar"], "float_map" => ["20" => 56.78], "obj_map" => ["a" => ["id" => 1], "b" => ["id" => 2]]];
  #endif
  var_dump($dump);
}

function test_array_map_null_object() {
  /** @var A[] */
  $dummy_arr = [];
  $null_a = $dummy_arr[0];

  $a1 = new A;
  $a1->id = 77;

  $a2 = new A;
  $a2->id = 44;

  $obj = new ArrayNullObject;
  $obj->vec = ["a" => $a1, "b" => $null_a, "c" => $a2];

  $json = to_json($obj);
  $restored_obj = from_json($json, "ArrayNullObject");
  $dump = to_array_debug($restored_obj);
  #ifndef KPHP
  $dump = ["vec" => ["a" => ["id" => 77], "b" => null, "c" => ["id" => 44]]];
  #endif
  var_dump($dump);
}

test_array_map();
test_array_map_null_object();
