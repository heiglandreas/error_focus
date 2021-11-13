<?php
/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org_Heigl\ErrorFocusTest;

use Org_Heigl\ErrorFocus\ErrorHandler;
use Org_Heigl\ErrorFocus\ErrorHandlerList;
use PHPUnit\Framework\TestCase;

class FunctionalTest extends TestCase
{
    /**
     * @testdox Triggering error inside scope does not cause an error to show up in the error-log
     * @covers \Org_Heigl\ErrorFocus\ErrorHandler::__invoke
     */
    public function testRaiseExceptionInClassInsideScope()
    {
        $errorHandler = ErrorHandler::fromString(__DIR__);

        $file =  tempnam(sys_get_temp_dir(), 'test');
        ini_set('error_log',$file);
        set_error_handler(function ($a, $b, $c, $d, $e) use ($file) {
            $handler = fopen($file, 'a');
            fwrite($handler, $b);
            fclose($handler);
        });

        $handler = fopen($file, 'r');
        self::assertEmpty(fread($handler, 1024));
        trigger_error('WTF');
        self::assertNotEmpty(fread($handler, 1024));

        set_error_handler($errorHandler);
        trigger_error('WTF');
        self::assertEmpty(fread($handler, 1024));

        fclose($handler);
        unlink($file);
    }

    /**
     * @testdox Triggering error outside scope does cause an error to show up in the error-log
     * @covers \Org_Heigl\ErrorFocus\ErrorHandler::__invoke
     */
    public function testRaiseExceptionInClassOutsideScope()
    {
        $errorHandler = ErrorHandler::fromString(__DIR__ . '/../src');

        $file =  tempnam(sys_get_temp_dir(), 'test');
        ini_set('error_log',$file);
        set_error_handler(function ($a, $b, $c, $d, $e) use ($file) {
            $handler = fopen($file, 'a');
            fwrite($handler, $b);
            fclose($handler);
        });

        $handler = fopen($file, 'r');
        self::assertEmpty(fread($handler, 1024));
        trigger_error('WTF');
        self::assertNotEmpty(fread($handler, 1024));

        set_error_handler($errorHandler);
        trigger_error('WTF');
        self::assertMatchesRegularExpression('/ WTF /', fread($handler, 1024));
        fclose($handler);
        unlink($file);
    }

    /**
     * @testdox Removing errors from multiple scopes works
     * @covers \Org_Heigl\ErrorFocus\ErrorHandler::__invoke
     */
    public function testRemoveErrorsFromMultipleScopes()
    {
        $errorHandler1 = ErrorHandler::fromString(__DIR__ . '/asset1');
        $errorHandler2 = ErrorHandler::fromString(__DIR__ . '/asset2');
        $errorHandler = new ErrorHandlerList();
        $errorHandler->addErrorHandler($errorHandler1);
        $errorHandler->addErrorHandler($errorHandler2);

        $file =  tempnam(sys_get_temp_dir(), 'test');
        ini_set('error_log',$file);
        set_error_handler(function ($a, $b, $c, $d, $e) use ($file) {
            $handler = fopen($file, 'a');
            fwrite($handler, $b);
            fclose($handler);
        });

        $handler = fopen($file, 'r');
        self::assertEmpty(fread($handler, 1024));
        trigger_error('WTF');
        self::assertNotEmpty(fread($handler, 1024));

        set_error_handler($errorHandler);
        require __DIR__ . '/asset1/error.php';
        self::assertEmpty(fread($handler, 1024));
        require __DIR__ . '/asset2/error.php';
        self::assertEmpty(fread($handler, 1024));

        fclose($handler);
        unlink($file);
    }
}
