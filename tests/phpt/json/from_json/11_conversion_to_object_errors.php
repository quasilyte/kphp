@kphp_runtime_should_warn
/from_json\(\) error: unexpected type number for variable 'object_f'/
/from_json\(\) error: unexpected type number for variable 'object_f'/
/from_json\(\) error: unexpected type boolean for variable 'object_f'/
/from_json\(\) error: unexpected type string for variable 'object_f'/
/from_json\(\) error: unexpected type array for variable 'object_f'/

<?php
require_once 'kphp_tester_include.php';

class B {}

class A {
  public B $object_f;
}

function test_conversion_to_object_errors() {
  $obj = from_json("{\"object_f\":12}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"object_f\":12.34}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"object_f\":true}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"object_f\":\"foo\"}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"object_f\":[]}", "A");
  var_dump($obj === null);
}

test_conversion_to_object_errors();
