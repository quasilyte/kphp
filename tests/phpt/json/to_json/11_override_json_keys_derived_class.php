@ok
<?php
require_once 'kphp_tester_include.php';

class B {
  public string $foo = "foo_value";
}

class A extends B {
  /** @kphp-json-field bar_json */
  public string $bar = "bar_value";
}

function test_override_json_keys_derived_class() {
  $json = to_json(new A);
  #ifndef KPHP
  $json = "{\"bar_json\":\"bar_value\",\"foo\":\"foo_value\"}";
  #endif
  var_dump($json);
}

test_override_json_keys_derived_class();
