@kphp_runtime_should_warn
/from_json\(\) error: unexpected type number for variable 'string_f'/
/from_json\(\) error: unexpected type number for variable 'string_f'/
/from_json\(\) error: unexpected type boolean for variable 'string_f'/
/from_json\(\) error: unexpected type object for variable 'string_f'/
/from_json\(\) error: unexpected type array for variable 'string_f'/

<?php
require_once 'kphp_tester_include.php';

class A {
  public string $string_f;
}

function test_conversion_to_string_errors() {
  $obj = from_json("{\"string_f\":12}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"string_f\":12.34}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"string_f\":true}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"string_f\":{}}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"string_f\":[]}", "A");
  var_dump($obj === null);
}

test_conversion_to_string_errors();
