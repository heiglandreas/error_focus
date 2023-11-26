<?php
/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org_Heigl\ErrorFocusTest;

use Org_Heigl\ErrorFocus\ErrorHandler;
use Org_Heigl\ErrorFocus\ErrorHandlerList;
use Org_Heigl\ErrorFocus\ErrorHandlerPath;
use PHPUnit\Framework\TestCase;
use function stream_filter_prepend;
use function stream_filter_register;
use function stream_filter_remove;
use function trigger_error;
use const STDERR;
use const STREAM_FILTER_ALL;
use const STREAM_FILTER_WRITE;

class FunctionalTests extends TestCase
{
    private const STREAM_FILTER = 'stream_filter_test';

	private $streamFilter = null;

    public function setUp(): void
    {
        parent::setUp();

        stream_filter_register(self::STREAM_FILTER, Tie::class);
        $this->streamFilter = stream_filter_prepend(STDERR, self::STREAM_FILTER, STREAM_FILTER_WRITE);
    }

    public function tearDown(): void
    {
        stream_filter_remove($this->streamFilter);

        parent::tearDown();
    }

    /**
     * @testdox Triggering error inside scope does not cause an error to show up in the error-log
     * @covers \Org_Heigl\ErrorFocus\ErrorHandler::__invoke
     */
    public function testRaiseExceptionInClassInsideScope()
    {
        $errorHandler = ErrorHandlerPath::fromString(__DIR__);

        set_error_handler($errorHandler);
        trigger_error('WTF');
        self::assertEmpty(Tie::$cache);
    }

    /**
     * @testdox Triggering error outside scope does cause an error to show up in the error-log
     * @covers \Org_Heigl\ErrorFocus\ErrorHandler::__invoke
     */
    public function testRaiseExceptionInClassOutsideScope()
    {
        $errorHandler = ErrorHandlerPath::fromString(__DIR__ . '/../src');

        set_error_handler($errorHandler);
        trigger_error('WTF');

		self::assertEmpty(Tie::$cache);
    }

    /**
     * @testdox Removing errors from multiple scopes works
     * @covers \Org_Heigl\ErrorFocus\ErrorHandlerPath::__invoke
     */
    public function testRemoveErrorsFromMultipleScopes()
    {
        $errorHandler1 = ErrorHandlerPath::fromString(__DIR__ . '/asset1');
        $errorHandler2 = ErrorHandlerPath::fromString(__DIR__ . '/asset2');
        $errorHandler = new ErrorHandlerList();
        $errorHandler->addErrorHandler($errorHandler1);
        $errorHandler->addErrorHandler($errorHandler2);

        $file =  tempnam(sys_get_temp_dir(), 'test');
        ini_set('error_log',$file);
        set_error_handler(function ($a, $b, $c, $d, $e = null) use ($file) {
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
