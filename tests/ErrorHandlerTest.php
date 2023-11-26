<?php
/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org_Heigl\ErrorFocusTest;

use Org_Heigl\ErrorFocus\ErrorHandlerPath;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

class ErrorHandlerTest extends TestCase
{
    /**
     * @testdox True is returned when the file-path begins with the configured base path
     * @covers \Org_Heigl\ErrorFocus\ErrorHandlerPath::__invoke
     */
    public function testContainedPathResultsInFalseBeingReturned()
    {
        $handler = ErrorHandlerPath::fromSplFileInfo(new SplFileInfo(__DIR__));

        $result = $handler(12, 'message', __FILE__, 42);

        self::assertTrue($result);
    }

    /**
     * @testdox False is returned when the file-path does not begin with the configured base path
     * @covers \Org_Heigl\ErrorFocus\ErrorHandlerCallback::__invoke
     */
    public function testNotContainedPathResultsInFalseBeingReturned()
    {
        $handler = ErrorHandlerPath::fromSplFileInfo(new SplFileInfo(__DIR__));

        $result = $handler(12, 'message', '/tmp', 42);

        self::assertFalse($result);
    }
}
