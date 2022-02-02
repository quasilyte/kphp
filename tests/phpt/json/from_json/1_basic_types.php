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
}

function test_empty_class() {
  $obj = from_json("{}", "EmptyClass");
  #ifndef KPHP
  $obj = new EmptyClass;
  #endif
  var_dump(to_array_debug($obj));
}

function test_basic_types() {
  $json = "{\"int_positive_f\":123,\"int_negative_f\":-42,\"bool_true_f\":true,\"bool_false_f\":false," .
            "\"string_f\":\"foo\",\"float_positive_f\":123.45,\"float_negative_f\":-98.76}";
  $obj = from_json($json, "BasicTypes");
  #ifndef KPHP
  $obj = new BasicTypes;
  $obj->init();
  #endif
  var_dump(to_array_debug($obj));
}

test_empty_class();
test_basic_types();
