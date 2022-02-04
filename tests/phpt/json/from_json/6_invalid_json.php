@kphp_runtime_should_warn
/from_json\(\) error: invalid json string at offset 18: Invalid value/
/from_json\(\) error: invalid json string at offset 26: Missing a comma or '}' after an object member/
/from_json\(\) error: root element must be an object type, got string/
/from_json\(\) error: root element must be an object type, got array/

<?php
require_once 'kphp_tester_include.php';

class A {}

function test_invalid_json() {
  $obj = from_json("{\"where_my_value\":}", "A");
  var_dump($obj === null);

  $obj = from_json("{\"unclosed_object\":\"value\"", "A");
  var_dump($obj === null);
}

function test_root_not_object() {
  $obj = from_json("\"foo\"", "A");
  var_dump($obj === null);

  $obj = from_json("[]", "A");
  var_dump($obj === null);
}

test_invalid_json();
test_root_not_object();
