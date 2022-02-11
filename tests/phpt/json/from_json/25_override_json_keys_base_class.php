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
}

function test_override_json_keys_base_class() {
  $json = "{\"a\":\"a_value\",\"b\":\"b_value\",\"c_json\":\"c_value\"}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["a" => "a_value", "b" => "b_value", "c" => "c_value"];
  #endif
  var_dump($dump);
}

test_override_json_keys_base_class();
