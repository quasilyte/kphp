@kphp_runtime_should_warn
/from_json\(\) error: unexpected type number for variable 'array_f'/
/from_json\(\) error: unexpected type number for variable 'array_f'/
/from_json\(\) error: unexpected type boolean for variable 'array_f'/
/from_json\(\) error: unexpected type string for variable 'array_f'/
/from_json\(\) error: unexpected type object for variable 'array_f'/

<?php
require_once 'kphp_tester_include.php';

class A {
  public int[] $array_f;
}

function test_conversion_to_array_errors() {
  $obj = from_json("{\"array_f\":12}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"array_f\":12.34}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"array_f\":true}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"array_f\":\"foo\"}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"array_f\":{}}", "A");
  var_dump($obj === null);
}

test_conversion_to_array_errors();
