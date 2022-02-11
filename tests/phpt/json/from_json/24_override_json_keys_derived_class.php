@ok
<?php
require_once 'kphp_tester_include.php';

class B {
  public string $foo;
}

class A extends B {
  /** @kphp-json-field bar_json */
  public string $bar;
}

function test_override_json_keys_derived_class() {
  $json = "{\"bar_json\":\"bar_value\",\"foo\":\"foo_value\"}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["bar" => "bar_value", "foo" => "foo_value"];
  #endif
  var_dump($dump);
}

test_override_json_keys_derived_class();
