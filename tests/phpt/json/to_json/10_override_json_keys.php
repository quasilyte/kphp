@ok
<?php
require_once 'kphp_tester_include.php';

class B {
  /** @kphp-json-field new_b_foo */
  public string $b_foo = "value1";
}

class C {
  /** @kphp-json-field new_c_bar */
  public float $c_bar = 98.76;
  // no tag
  public bool $c_baz = false;
}

class A extends C {
  // no tag
  public string $foo = "value2";
  /** @kphp-json-field */
  public float $bar = 12.34;
  /** @kphp-json-field new_baz */
  public bool $baz = true;
  /** @kphp-json-field new_b */
  public B $b;

  function __construct() {
    $this->$b = new B;
  }
}

function test_override_json_keys() {
  $json = to_json(new A);
  #ifndef KPHP
  $json = "{\"foo\":\"value2\",\"bar\":12.34,\"new_baz\":true,\"new_b\":{\"new_b_foo\":\"value1\"},\"new_c_bar\":98.76,\"c_baz\":false}";
  #endif
  var_dump($json);
}

test_override_json_keys();
