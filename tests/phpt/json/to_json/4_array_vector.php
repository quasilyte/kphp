@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public int $id = 0;
  function __construct($id) {
    $this->id = $id;
  }
}

class ArrayVector {
  public $empty_vec = [];
  public $int_vec = [11, 22, 33];
  public $bool_vec = [true, false];
  public $string_vec = ["foo", "bar"];
  public $float_vec = [56.78];
  public $obj_vec = [];

  function __construct() {
    $this->obj_vec = [new A(1), new A(2)];
  }
}

class ArrayNullObject {
  public $vec = [];

  function __construct() {
    /** @var A[] */
    $dummy_arr = [];
    $null_a = $dummy_arr[0];
    $this->vec = [new A(1), $null_a];
  }
}

function test_array_vector() {
  $json = to_json(new ArrayVector);
  #ifndef KPHP
  $json = "{\"empty_vec\":[],\"int_vec\":[11,22,33],\"bool_vec\":[true,false]," .
   "\"string_vec\":[\"foo\",\"bar\"],\"float_vec\":[56.78],\"obj_vec\":[{\"id\":1},{\"id\":2}]}";
  #endif
  echo $json;
}

function test_array_vector_null_object() {
  $json = to_json(new ArrayNullObject);
  #ifndef KPHP
  $json = "{\"vec\":[{\"id\":1},null]}";
  #endif
  echo $json;
}

test_array_vector();
test_array_vector_null_object();
