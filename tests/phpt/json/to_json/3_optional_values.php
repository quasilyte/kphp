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
  /** @var ?int[] */
  public $array_f;
}

function test_optional_values() {
  $obj = new OptionalTypes;
  $json = to_json($obj);
  #ifndef KPHP
  $json = "{\"int_f\":null,\"bool_f\":null,\"string_f\":null,\"float_f\":null,\"a_f\":null,\"array_f\":null}";
  #endif
  echo $json;

  $obj->int_f = 123;
  $obj->bool_f = true;
  $obj->string_f = "foo";
  $obj->float_f = 123.45;
  $obj->a_f = new A;
  $obj->array_f = [11, 33];
  $json = to_json($obj);
  #ifndef KPHP
  $json = "{\"int_f\":123,\"bool_f\":true,\"string_f\":\"foo\",\"float_f\":123.45,\"a_f\":{},\"array_f\":[11,33]}";
  #endif
  echo $json;
}

test_optional_values();
