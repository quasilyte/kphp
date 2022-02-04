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

  function init() {
   $this->empty_vec = [];
   $this->int_vec = ["a" => 11, "b" => 22, "c" => 33];
   $this->bool_vec = [true, false];
   $this->string_vec = ["a" => "foo", "b" => "bar"];
   $this->float_vec = [56.78];
  }
}

class MixedArrayOfArrays {
  /** @var mixed */
  public $arr;

  function init() {
    $this->arr[] = [11, 22];
    $this->arr[] = [33, 44];
  }
}

function test_array_of_null_mixed() {
  $obj = new MixedArrays;
  $json = to_json($obj);
  $restored_obj = from_json($json, "MixedArrays");
  $dump = to_array_debug($restored_obj);

  #ifndef KPHP
  $dump = ["empty_vec" => null, "int_vec" => null, "bool_vec" => null,
   "string_vec" => null, "float_vec" => null];
  #endif
  var_dump($dump);

  $obj = new MixedArrays;
  $obj->empty_vec = null;
  $obj->int_vec = null;
  $obj->bool_vec = null;
  $obj->string_vec = null;
  $obj->float_vec = null;
  $json = to_json($obj);
  $restored_obj = from_json($json, "MixedArrays");
  $dump = to_array_debug($restored_obj);

  #ifndef KPHP
  $dump = ["empty_vec" => null, "int_vec" => null, "bool_vec" => null,
   "string_vec" => null, "float_vec" => null];
  #endif
  var_dump($dump);
}

function test_array_of_mixed() {
  $obj = new MixedArrays;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "MixedArrays");
  $dump = to_array_debug($restored_obj);

  #ifndef KPHP
  $dump = ["empty_vec" => [], "int_vec" => ["a" => 11, "b" => 22, "c" => 33], "bool_vec" => [true, false],
   "string_vec" => ["a" => "foo", "b" => "bar"], "float_vec" => [56.78]];
  #endif
  var_dump($dump);
}

function test_array_of_arrays_mixed() {
  $obj = new MixedArrayOfArrays;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "MixedArrayOfArrays");
  $dump = to_array_debug($restored_obj);
  #ifndef KPHP
  $dump = ["arr" => [[11, 22], [33, 44]]];
  #endif
  var_dump($dump);
}

test_array_of_null_mixed();
test_array_of_mixed();
test_array_of_arrays_mixed();
