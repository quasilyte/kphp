@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public string $a;
  public float $b;
  public bool $c;
}

function test_pretty_json_format_ok() {
  $json = "{\n\t\"a\": \"foo\",\n\t\"b\": 12.34,\n\t\"c\": true\n}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["a" => "foo", "b" => 12.34, "c" => true];
  #endif
  var_dump($dump);
}

test_pretty_json_format_ok();
