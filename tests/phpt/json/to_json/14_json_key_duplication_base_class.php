@kphp_should_fail
/kphp-json-field 'a' met twice, first time in class 'C'/
<?php

class C {
  /** @kphp-json-field a */
  public string $c = "c_value";
}

class B extends C {
  public string $b = "b_value";
}

class A extends B {
  public string $a = "a_value";
}

function test_json_key_duplication_base_class() {
  $json = to_json(new A);
}

test_json_key_duplication_base_class();
