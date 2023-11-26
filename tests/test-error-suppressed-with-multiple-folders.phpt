--TEST--
Error Suppressed when raised within one of multiple defined folders
--FILE--
<?php
require_once __DIR__ . '/../vendor/autoload.php';

$errorHandler = new \Org_Heigl\ErrorFocus\ErrorHandlerList();
$errorHandler->addErrorHandler(\Org_Heigl\ErrorFocus\ErrorHandlerPath::fromString(__DIR__ . '/asset1'));
$errorHandler->addErrorHandler(\Org_Heigl\ErrorFocus\ErrorHandlerPath::fromString(__DIR__ . '/asset2'));

echo 'bar';
set_error_handler($errorHandler);

include __DIR__ . '/asset2/error.php';
?>
--EXPECT--
bar
