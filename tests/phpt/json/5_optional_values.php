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
  $restored_obj = from_json($json, "OptionalTypes");
  #ifndef KPHP
  $restored_obj = new OptionalTypes;
  $restored_obj->int_f = null;
  $restored_obj->bool_f = null;
  $restored_obj->string_f = null;
  $restored_obj->float_f = null;
  $restored_obj->a_f = null;
  $restored_obj->array_f = null;
  #endif
  var_dump(to_array_debug($restored_obj));

  $obj = new OptionalTypes;
  $obj->int_f = null;
  $obj->bool_f = null;
  $obj->string_f = null;
  $obj->float_f = null;
  $obj->a_f = null;
  $obj->array_f = null;
  $json = to_json($obj);
  $restored_obj = from_json($json, "OptionalTypes");
  #ifndef KPHP
  $restored_obj = $obj;
  #endif
  var_dump(to_array_debug($restored_obj));

  $obj = new OptionalTypes;
  $obj->int_f = 123;
  $obj->bool_f = true;
  $obj->string_f = "foo";
  $obj->float_f = 123.45;
  $obj->a_f = new A;
  $obj->array_f = [33, 55];
  $json = to_json($obj);
  $restored_obj = from_json($json, "OptionalTypes");
  #ifndef KPHP
  $restored_obj = $obj;
  #endif
  var_dump(to_array_debug($restored_obj));
}

test_optional_values();
