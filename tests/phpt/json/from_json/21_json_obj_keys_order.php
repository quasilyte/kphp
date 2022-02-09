@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public string $a;
  public string $b;
  public string $c;
}

function test_direct_order() {
  $json = "{\"a\":\"aaa\",\"b\":\"bbb\",\"c\":\"ccc\"}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["a" => "aaa", "b" => "bbb", "c" => "ccc"];
  #endif
  var_dump($dump);
}

function test_mismatch_order() {
  $jsons = [
    "{\"a\":\"aaa\",\"c\":\"ccc\",\"b\":\"bbb\"}",
    "{\"b\":\"bbb\",\"a\":\"aaa\",\"c\":\"ccc\"}",
    "{\"b\":\"bbb\",\"c\":\"ccc\",\"a\":\"aaa\"}",
    "{\"c\":\"ccc\",\"b\":\"bbb\",\"a\":\"aaa\"}",
    "{\"c\":\"ccc\",\"a\":\"aaa\",\"b\":\"bbb\"}"
  ];

  foreach ($jsons as $json) {
    $obj = from_json($json, "A");
    $dump = to_array_debug($obj);
    #ifndef KPHP
    $dump = ["a" => "aaa", "b" => "bbb", "c" => "ccc"];
    #endif
    var_dump($dump);
  }
}

function test_excessive_json_keys_direct_order() {
  $json = "{\"foo\":\"foo\",\"a\":\"aaa\",\"b\":\"bbb\",\"bar\":\"bar\",\"c\":\"ccc\",\"baz\":\"baz\"}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["a" => "aaa", "b" => "bbb", "c" => "ccc"];
  #endif
  var_dump($dump);
}

function test_excessive_json_keys_mismatch_order() {
  $json = "{\"foo\":\"foo\",\"c\":\"ccc\",\"b\":\"bbb\",\"bar\":\"bar\",\"a\":\"aaa\",\"baz\":\"baz\"}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["a" => "aaa", "b" => "bbb", "c" => "ccc"];
  #endif
  var_dump($dump);
}

test_direct_order();
test_mismatch_order();
test_excessive_json_keys_direct_order();
test_excessive_json_keys_mismatch_order();
