@ok
<?php
require_once 'kphp_tester_include.php';

class C {
  /** @kphp-json-field c_json */
  public string $c;
}

class B extends C {
  public string $b;
}

class A extends B {
  public string $a;

  function init() {
    $this->a = "a_value";
    $this->b = "b_value";
    $this->c = "c_value";
  }
}

function test_override_json_keys_base_class() {
  $obj = new A;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "A");
  $dump = to_array_debug($restored_obj);
  #ifndef KPHP
  $dump = ["a" => "a_value", "b" => "b_value", "c" => "c_value"];
  #endif
  var_dump($dump);
}

test_override_json_keys_base_class();
