@ok
<?php
require_once 'kphp_tester_include.php';

class A {
  public float $nan_f;
  public float $inf_f;
  public float $minus_inf_f;
  public float $inf_f2;
  public float $minus_inf_f2;
}

function test_nan_inf() {
  $json = "{\"nan_f\":NaN,\"inf_f\":Infinity,\"minus_inf_f\":-Infinity,\"inf_f2\":Inf,\"minus_inf_f2\":-Inf}";
  $obj = from_json($json, "A");
  $dump = to_array_debug($obj);
  #ifndef KPHP
  $dump = ["nan_f" => NAN, "inf_f" => INF, "minus_inf_f" => -INF, "inf_f2" => INF, "minus_inf_f2" => -INF];
  #endif
  var_dump($dump);
}

test_nan_inf();
