@kphp_should_fail
KPHP_ENABLE_FFI=1
/Too few arguments to function call, expected 2, have 1/
<?php

$cdef = FFI::cdef('
  #define FFI_SCOPE "example"
  void f(int x, int y);
');

$cdef->f(1);
