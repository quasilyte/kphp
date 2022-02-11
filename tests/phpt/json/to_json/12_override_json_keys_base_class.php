@ok
<?php
require_once 'kphp_tester_include.php';

class C {
  /** @kphp-json-field c_json */
  public string $c = "c_value";
}

class B extends C {
  public string $b = "b_value";
}

class A extends B {
  public string $a = "a_value";
}

function test_override_json_keys_base_class() {
  $json = to_json(new A);
  #ifndef KPHP
  $json = "{\"a\":\"a_value\",\"b\":\"b_value\",\"c_json\":\"c_value\"}";
  #endif
  var_dump($json);
}

test_override_json_keys_base_class();
