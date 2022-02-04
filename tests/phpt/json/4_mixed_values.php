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
  $restored_obj = from_json($json, "MixedValue");
  #ifndef KPHP
  $restored_obj = $obj;
  #endif
  var_dump(to_array_debug($restored_obj));

  $obj = new MixedValue;
  $obj->mixed_value = null;
  $json = to_json($obj);
  $restored_obj = from_json($json, "MixedValue");
  #ifndef KPHP
  $restored_obj = $obj;
  #endif
  var_dump(to_array_debug($restored_obj));

  $obj = new MixedValue;
  $obj->mixed_value = true;
  $json = to_json($obj);
  $restored_obj = from_json($json, "MixedValue");
  #ifndef KPHP
  $restored_obj = $obj;
  #endif
  var_dump(to_array_debug($restored_obj));

  $obj = new MixedValue;
  $obj->mixed_value = 12345;
  $json = to_json($obj);
  $restored_obj = from_json($json, "MixedValue");
  #ifndef KPHP
  $restored_obj = $obj;
  #endif
  var_dump(to_array_debug($restored_obj));

  $obj = new MixedValue;
  $obj->mixed_value = 987.65;
  $json = to_json($obj);
  $restored_obj = from_json($json, "MixedValue");
  #ifndef KPHP
  $restored_obj = $obj;
  #endif
  var_dump(to_array_debug($restored_obj));

  $obj = new MixedValue;
  $obj->mixed_value = "foo";
  $json = to_json($obj);
  $restored_obj = from_json($json, "MixedValue");
  #ifndef KPHP
  $restored_obj = $obj;
  #endif
  var_dump(to_array_debug($restored_obj));
}

test_mixed_values();
