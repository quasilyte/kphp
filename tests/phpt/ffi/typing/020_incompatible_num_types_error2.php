@kphp_should_fail
KPHP_ENABLE_FFI=1
/Invalid php2c conversion in this context: int -> double/
<?php

$cdef = FFI::cdef('struct Foo { double field; };');

$foo = $cdef->new('struct Foo');
$foo->field = 0;
