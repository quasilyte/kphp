@ok
<?php
require_once 'kphp_tester_include.php';

class AOld {
  /** @kphp-json-field foo_json */
  public string $foo;
  /** @kphp-json-field bar_json */
  public string $bar;

  function init() {
    $this->foo = "foo_value";
    $this->bar = "bar_value";
  }
}

class ANew {
  /** @kphp-json-field foo_json */
  public string $foo_renamed;
  /** @kphp-json-field bar_json */
  public string $bar_renamed;
}

function test_variable_renaming() {
  $obj = new AOld;
  $obj->init();
  $json = to_json($obj);

  $obj_restored = from_json($json, "ANew");
  $dump = to_array_debug($obj_restored);
  #ifndef KPHP
  $dump = ["foo_renamed" => "foo_value", "bar_renamed" => "bar_value"];
  #endif
  var_dump($dump);
}

test_variable_renaming();
