--TEST--
Error Suppressed when raised within defined folder
--FILE--
<?php
require_once __DIR__ . '/../vendor/autoload.php';

$errorHandler = \Org_Heigl\ErrorFocus\ErrorHandlerPath::fromString(__DIR__ . '/asset1');

set_error_handler($errorHandler);

include __DIR__ . '/asset2/error.php';
?>
--EXPECTF--
Notice: Foo in %s
