@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public float $nan_f = NAN;
  public float $inf_f = INF;
  public float $plus_inf_f = +INF;
  public float $minus_inf_f = -INF;
}

function test_nan_inf() {
  $json = to_json(new A);
  #ifndef KPHP
  $json = "{\"nan_f\":NaN,\"inf_f\":Infinity,\"plus_inf_f\":Infinity,\"minus_inf_f\":-Infinity}";
  #endif
  var_dump($json);
}

test_nan_inf();
