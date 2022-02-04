@ok
<?php
require_once 'kphp_tester_include.php';

class EmptyClass {}

class BasicTypes {
  public int $int_positive_f;
  public int $int_negative_f;
  public bool $bool_true_f;
  public bool $bool_false_f;
  public string $string_f;
  public float $float_positive_f;
  public float $float_negative_f;

  function init() {
    $this->int_positive_f = 123;
    $this->int_negative_f = -42;
    $this->bool_true_f = true;
    $this->bool_false_f = false;
    $this->string_f = "foo";
    $this->float_positive_f = 123.45;
    $this->float_negative_f = -98.76;
  }

  /** @param BasicTypes $other */
  public function is_equal($other) {
    var_dump($other->int_positive_f);
    var_dump($other->int_negative_f);
    var_dump($other->bool_true_f);
    var_dump($other->bool_false_f);
    var_dump($other->string_f);
    var_dump($other->float_positive_f);
    var_dump($other->float_negative_f);

    return $this->int_positive_f === $other->int_positive_f
        && $this->int_negative_f === $other->int_negative_f
        && $this->bool_true_f === $other->bool_true_f
        && $this->bool_false_f === $other->bool_false_f
        && $this->string_f === $other->string_f
        && $this->float_positive_f === $other->float_positive_f
        && $this->float_negative_f === $other->float_negative_f;
  }
}

function test_empty_class() {
  $obj = new EmptyClass;
  $json = to_json($obj);
  $restored_obj = from_json($json, "EmptyClass");
  #ifndef KPHP
  $restored_obj = new EmptyClass;
  #endif
  var_dump(to_array_debug($restored_obj));
}

function test_basic_types_defaults() {
  $obj = new BasicTypes;
  $json = to_json($obj);
  $restored_obj = from_json($json, "BasicTypes");

  $dump = to_array_debug($restored_obj);

  #ifndef KPHP
  $dump = ["int_positive_f" => 0, "int_negative_f" => 0, "bool_true_f" => false, "bool_false_f" => false,
   "string_f" => "", "float_positive_f" => 0.0, "float_negative_f" => 0.0];
  #endif

  var_dump($dump);
}

function test_basic_types() {
  $obj = new BasicTypes;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "BasicTypes");
  #ifndef KPHP
  $restored_obj = $obj;
  #endif
  var_dump($obj->is_equal($restored_obj));
}

test_empty_class();
test_basic_types_defaults();
test_basic_types();
