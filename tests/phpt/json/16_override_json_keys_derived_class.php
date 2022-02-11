@ok
<?php
require_once 'kphp_tester_include.php';

class B {
  public string $foo;
}

class A extends B {
  /** @kphp-json-field bar_json */
  public string $bar;

  function init() {
    $this->bar = "bar_value";
    $this->foo = "foo_value";
  }
}

function test_override_json_keys_derived_class() {
  $obj = new A;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "A");
  $dump = to_array_debug($restored_obj);
  #ifndef KPHP
  $dump = ["bar" => "bar_value", "foo" => "foo_value"];
  #endif
  var_dump($dump);
}

test_override_json_keys_derived_class();
