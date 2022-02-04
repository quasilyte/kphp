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

  function init() {
    $this->int_vec = [11, 22, 33];
    $this->bool_vec = [true, false];
    $this->string_vec = ["foo", "bar"];
    $this->float_vec = [56.78];
    $a1 = new A;
    $a1->id = 1;
    $a2 = new A;
    $a2->id = 2;
    $this->obj_vec = [$a1, $a2];
  }
}

class ArrayNullObject {
  /** @var A[] */
  public $vec = [];
}

function test_array_vector() {
  $obj = new ArrayVector;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "ArrayVector");
  $dump = to_array_debug($restored_obj);
  #ifndef KPHP
  $dump = ["empty_vec" => [], "int_vec" => [11,22,33], "bool_vec" => [true, false],
   "string_vec" => ["foo", "bar"], "float_vec" => [56.78], "obj_vec" => [["id" => 1], ["id" => 2]]];
  #endif
  var_dump($dump);
}

function test_array_vector_null_object() {
  /** @var A[] */
  $dummy_arr = [];
  $null_a = $dummy_arr[0];

  $a1 = new A;
  $a1->id = 77;

  $a2 = new A;
  $a2->id = 44;

  $obj = new ArrayNullObject;
  $obj->vec = [$a1, $null_a, $a2];

  $json = to_json($obj);
  $restored_obj = from_json($json, "ArrayNullObject");
  $dump = to_array_debug($restored_obj);
  #ifndef KPHP
  $dump = ["vec" => [["id" => 77], null, ["id" => 44]]];
  #endif
  var_dump($dump);
}

test_array_vector();
test_array_vector_null_object();
