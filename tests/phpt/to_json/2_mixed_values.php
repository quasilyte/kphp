@ok
<?php
require_once 'kphp_tester_include.php';

class MixedValue {
  /** @var mixed */
  public $mixed_value;
}

function test_mixed_values() {
  $obj = new MixedValue;

  $json = to_json($obj);
  #ifndef KPHP
  $json = "{\"mixed_value\":null}";
  #endif
  echo $json;

  $obj->mixed_value = true;
  $json = to_json($obj);
  #ifndef KPHP
  $json = "{\"mixed_value\":true}";
  #endif
  echo $json;

  $obj->mixed_value = 12345;
  $json = to_json($obj);
  #ifndef KPHP
  $json = "{\"mixed_value\":12345}";
  #endif
  echo $json;

  $obj->mixed_value = 987.65;
  $json = to_json($obj);
  #ifndef KPHP
  $json = "{\"mixed_value\":987.65}";
  #endif
  echo $json;

  $obj->mixed_value = 987.65;
  $json = to_json($obj);
  #ifndef KPHP
  $json = "{\"mixed_value\":987.65}";
  #endif
  echo $json;

  $obj->mixed_value = "foo";
  $json = to_json($obj);
  #ifndef KPHP
  $json = "{\"mixed_value\":\"foo\"}";
  #endif
  echo $json;
}

test_mixed_values();
