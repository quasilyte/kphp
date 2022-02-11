@kphp_should_fail
/kphp-json-field 'a' met twice, first time in class 'A'/
<?php

class C {
  public string $c = "c_value";
}

class B extends C {
  public string $b = "b_value";
}

class A extends B {
  public string $a = "a_value";
  /** @kphp-json-field a */
  public string $d = "d_value";
}

function test_json_key_duplication() {
  $json = to_json(new A);
}

test_json_key_duplication();
