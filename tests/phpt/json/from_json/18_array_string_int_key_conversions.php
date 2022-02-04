@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  /** @var string[] */
  public $map;
}

function test_array_string_int_key_conversion() {
  $json = "{\"map\":{\"77\":\"foo\",\"88\":\"bar\",\"99\":\"baz\",\"key\":\"value\"}}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["map" => [77 => "foo", 88 => "bar", "99" => "baz", "key" => "value"]];
  #endif
  var_dump($dump);
}

test_array_string_int_key_conversion();
