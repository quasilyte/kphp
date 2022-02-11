@ok
<?php
require_once 'kphp_tester_include.php';

class B {
  /** @kphp-json-field new_b_foo */
  public string $b_foo;
}

class C {
  /** @kphp-json-field new_c_bar */
  public float $c_bar;
  // no tag
  public bool $c_baz;
}

class A extends C {
  // no tag
  public string $foo;
  /** @kphp-json-field */
  public float $bar;
  /** @kphp-json-field new_baz */
  public bool $baz;
  /** @kphp-json-field new_b */
  public B $b;
}

function test_override_json_keys() {
  $json = "{\"foo\":\"value2\",\"bar\":12.34,\"new_baz\":true,\"new_b\":{\"new_b_foo\":\"value1\"},\"new_c_bar\":98.76,\"c_baz\":false}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["foo" => "value2", "bar" => 12.34, "baz" => true, "b" => ["b_foo" => "value1"], "c_bar" => 98.76, "c_baz" => false];
  #endif
  var_dump($dump);
}

test_override_json_keys();
