@kphp_runtime_should_warn
/from_json\(\) error: unexpected type number for variable 'float_f'/
/from_json\(\) error: unexpected type boolean for variable 'float_f'/
/from_json\(\) error: unexpected type string for variable 'float_f'/
/from_json\(\) error: unexpected type object for variable 'float_f'/
/from_json\(\) error: unexpected type array for variable 'float_f'/

<?php
require_once 'kphp_tester_include.php';

class A {
  public float $float_f;
}

function test_conversion_to_float_errors() {
  $obj = from_json("{\"float_f\":12}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"float_f\":true}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"float_f\":\"foo\"}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"float_f\":{}}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"float_f\":[]}", "A");
  var_dump($obj === null);
}

test_conversion_to_float_errors();
