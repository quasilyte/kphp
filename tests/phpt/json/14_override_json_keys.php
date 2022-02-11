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

  function init() {
    $this->foo = "value2";
    $this->bar = 12.34;
    $this->baz = true;
    $this->b = new B;
    $this->b->b_foo = "value1";
    $this->c_bar = 98.76;
    $this->c_baz = false;
  }
}

function test_override_json_keys() {
  $obj = new A;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "A");
  $dump = to_array_debug($restored_obj);
  #ifndef KPHP
  $dump = ["foo" => "value2", "bar" => 12.34, "baz" => true, "b" => ["b_foo" => "value1"], "c_bar" => 98.76, "c_baz" => false];
  #endif
  var_dump($dump);
}

test_override_json_keys();
