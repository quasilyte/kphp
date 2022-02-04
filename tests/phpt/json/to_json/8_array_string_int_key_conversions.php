@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public $map = [77 => "foo", 88 => "bar", "99" => "baz", "key" => "value"];
}


function test_array_string_int_key_conversion() {
  $json = to_json(new A);
  #ifndef KPHP
  $json = "{\"map\":{\"77\":\"foo\",\"88\":\"bar\",\"99\":\"baz\",\"key\":\"value\"}}";
  #endif
  var_dump($json);
}

test_array_string_int_key_conversion();
