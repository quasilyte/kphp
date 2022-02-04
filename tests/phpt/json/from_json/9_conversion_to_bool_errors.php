@kphp_runtime_should_warn
/from_json\(\) error: unexpected type number for variable 'bool_f'/
/from_json\(\) error: unexpected type number for variable 'bool_f'/
/from_json\(\) error: unexpected type string for variable 'bool_f'/
/from_json\(\) error: unexpected type object for variable 'bool_f'/
/from_json\(\) error: unexpected type array for variable 'bool_f'/

<?php
require_once 'kphp_tester_include.php';

class A {
  public bool $bool_f;
}

function test_conversion_to_bool_errors() {
  $obj = from_json("{\"bool_f\":12}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"bool_f\":12.34}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"bool_f\":\"foo\"}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"bool_f\":{}}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"bool_f\":[]}", "A");
  var_dump($obj === null);
}

test_conversion_to_bool_errors();
