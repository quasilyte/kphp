@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public int $id = 0;
  function __construct($id) {
    $this->id = $id;
  }
}

class MixedArrays {
  /** @var mixed */
  public $empty_vec = [];
  /** @var mixed */
  public $int_vec = [11, 22, 33];
  /** @var mixed */
  public $bool_vec = [true, false];
  /** @var mixed */
  public $string_vec = ["foo", "bar"];
  /** @var mixed */
  public $float_vec = [56.78];
}

class MixedArrayOfArrays {
  /** @var mixed */
  public $arr;

  function __construct() {
      /** @var mixed */
    $a = [11, 22];
      /** @var mixed */
    $b = [33, 44];
    $this->arr = [$a, $b];
  }
}

function test_array_of_mixed() {
  $json = to_json(new MixedArrays);
  #ifndef KPHP
  $json = "{\"empty_vec\":[],\"int_vec\":[11,22,33],\"bool_vec\":[true,false]," .
    "\"string_vec\":[\"foo\",\"bar\"],\"float_vec\":[56.78]}";
  #endif
  echo $json;
}

function test_array_of_arrays_mixed() {
  $json = to_json(new MixedArrayOfArrays);
  #ifndef KPHP
  $json = "{\"arr\":[[11,22],[33,44]]}";
  #endif
  echo $json;
}

test_array_of_mixed();
test_array_of_arrays_mixed();
