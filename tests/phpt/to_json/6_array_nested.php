@ok
<?php
require_once 'kphp_tester_include.php';

class C {
  public int $idx = 0;
  function __construct($i) {
    $this->idx = $i;
  }
}

class B {
  public $arr_of_c;
  function __construct($i) {
    $this->arr_of_c = [new C($i), new C($i * 10)];
  }
}

class A {
  public $arr_of_b;
  function __construct() {
    $this->arr_of_b = ["a" => new B(1), "b" => new B(2)];
  }
}

class D {
  public $arr;
  function __construct() {
    $this->arr[] = [new C(1), new C(2)];
    $this->arr[] = [new C(3), new C(4)];
  }
}

function test_array_nested() {
  $json = to_json(new A);
  #ifndef KPHP
  $json = "{\"arr_of_b\":{\"a\":{\"arr_of_c\":[{\"idx\":1},{\"idx\":10}]},\"b\":{\"arr_of_c\":[{\"idx\":2},{\"idx\":20}]}}}";
  #endif
  echo $json;
}

function test_2_dimensional_array() {
  $json = to_json(new D);
  #ifndef KPHP
  $json = "{\"arr\":[[{\"idx\":1},{\"idx\":2}],[{\"idx\":3},{\"idx\":4}]]}";
  #endif
  echo $json;
}

test_array_nested();
test_2_dimensional_array();
