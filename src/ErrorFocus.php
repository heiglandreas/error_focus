<?php

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org_Heigl\ErrorFocus;

class ErrorFocus
{
    public static function init($paths = [], $additionalErrorHandlers = [])
    {
        $list = new ErrorHandlerList();
        foreach ($paths as $path) {
            $path = realpath($path);
            if (false === $path) {
                // The path does not exist, so we'll ignore it
                continue;
            }
            $list->addErrorHandler(ErrorHandler::fromString(realpath()));
        }

        foreach ($additionalErrorHandlers as $handler) {
            $list->addAdditionalErrorHandler($handler);
        }

        $previousHandler = set_error_handler($list);
	if (null !== $previousHandler) {
	    $list->addAdditionalErrorHandler($previousHandler);
	}
    }
}
