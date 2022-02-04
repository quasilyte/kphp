@ok
<?php
require_once 'kphp_tester_include.php';

class C {
  public int $idx = 0;
  function init($i) {
    $this->idx = $i;
  }
}

class B {
  public $arr_of_c;
  function init($i) {
    $c1 = new C;
    $c1->init($i);
    $c2 = new C;
    $c2->init($i * 10);
    $this->arr_of_c = [$c1, $c2];
  }
}

class A {
  public $arr_of_b;
  function init() {
    $b1 = new B;
    $b1->init(1);
    $b2 = new B;
    $b2->init(2);
    $this->arr_of_b = ["a" => $b1, "b" => $b2];
  }
}

class D {
  public $arr;
  function init() {

    $c1 = new C;
    $c1->init(1);

    $c2 = new C;
    $c2->init(2);

    $c3 = new C;
    $c3->init(3);

    $c4 = new C;
    $c4->init(4);

    $this->arr[] = [$c1, $c2];
    $this->arr[] = [$c3, $c4];
  }
}


function test_array_nested() {
  $obj = new A;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "A");
  $dump = to_array_debug($restored_obj);

  #ifndef KPHP
  $dump = ["arr_of_b" => ["a" => ["arr_of_c" => [["idx" => 1], ["idx" => 10]]], "b" => ["arr_of_c" => [["idx" => 2], ["idx" => 20]]]]];
  #endif

  var_dump($dump);
}

function test_2_dimensional_array() {
  $obj = new D;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "D");
  $dump = to_array_debug($restored_obj);

  #ifndef KPHP
  $dump = ["arr" => [[["idx" => 1], ["idx" => 2]], [["idx" => 3], ["idx" => 4]]]];
  #endif

  var_dump($dump);
}

test_array_nested();
test_2_dimensional_array();
