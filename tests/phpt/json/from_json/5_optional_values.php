@ok
<?php
require_once 'kphp_tester_include.php';

class A {}

class OptionalTypes {
  public ?int $int_f;
  public ?bool $bool_f;
  public ?string $string_f;
  public ?float $float_f;
  public ?A $a_f;
}

function test_optional_values() {
  $obj = from_json("{}", "OptionalTypes");
  #ifndef KPHP
  $obj = new OptionalTypes;
  $obj->int_f = null;
  $obj->bool_f = null;
  $obj->string_f = null;
  $obj->float_f = null;
  $obj->a_f = null;
  #endif
  var_dump(to_array_debug($obj));

  $obj = from_json("{\"int_f\":null,\"bool_f\":null,\"string_f\":null,\"float_f\":null,\"a_f\":null}", "OptionalTypes");
  #ifndef KPHP
  $obj = new OptionalTypes;
  $obj->int_f = null;
  $obj->bool_f = null;
  $obj->string_f = null;
  $obj->float_f = null;
  $obj->a_f = null;
  #endif
  var_dump(to_array_debug($obj));

  $obj = from_json("{\"int_f\":123,\"bool_f\":true,\"string_f\":\"foo\",\"float_f\":123.45,\"a_f\":{}}", "OptionalTypes");
  #ifndef KPHP
  $obj = new OptionalTypes;
  $obj->int_f = 123;
  $obj->bool_f = true;
  $obj->string_f = "foo";
  $obj->float_f = 123.45;
  $obj->a_f = new A;
  #endif
  var_dump(to_array_debug($obj));
}

test_optional_values();
