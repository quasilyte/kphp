@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public float $nan_f;
  public float $inf_f;
  public float $plus_inf_f;
  public float $minus_inf_f;

  function init() {
    $this->nan_f = NAN;
    $this->inf_f = INF;
    $this->plus_inf_f = +INF;
    $this->minus_inf_f = -INF;
  }
}

function test_nan_inf() {
  $obj = new A;
  $obj->init();
  $json = to_json($obj);
  $restored_obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["nan_f" => NAN, "inf_f" => INF, "plus_inf_f" => INF, "minus_inf_f" => -INF];
  #endif
  var_dump($dump);
}

test_nan_inf();
