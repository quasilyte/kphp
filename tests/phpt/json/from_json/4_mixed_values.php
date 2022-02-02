@ok
<?php
require_once 'kphp_tester_include.php';

class MixedValue {
  /** @var mixed */
  public $mixed_value;
}

function test_mixed_values() {
  $obj = from_json("{}", "MixedValue");
  #ifndef KPHP
  $obj = new MixedValue;
  #endif
  var_dump(to_array_debug($obj));

  $obj = from_json("{\"mixed_value\":null}", "MixedValue");
  #ifndef KPHP
  $obj = new MixedValue;
  #endif
  var_dump(to_array_debug($obj));

  $obj = from_json("{\"mixed_value\":true}", "MixedValue");
  #ifndef KPHP
  $obj = new MixedValue;
  $obj->mixed_value = true;
  #endif
  var_dump(to_array_debug($obj));

  $obj = from_json("{\"mixed_value\":12345}", "MixedValue");
  #ifndef KPHP
  $obj = new MixedValue;
  $obj->mixed_value = 12345;
  #endif
  var_dump(to_array_debug($obj));

  $obj = from_json("{\"mixed_value\":987.65}", "MixedValue");
  #ifndef KPHP
  $obj = new MixedValue;
  $obj->mixed_value = 987.65;
  #endif
  var_dump(to_array_debug($obj));

  $obj = from_json("{\"mixed_value\":\"foo\"}", "MixedValue");
  #ifndef KPHP
  $obj = new MixedValue;
  $obj->mixed_value = "foo";
  #endif
  var_dump(to_array_debug($obj));
}

test_mixed_values();
