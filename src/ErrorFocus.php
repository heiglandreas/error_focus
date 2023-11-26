<?php

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org_Heigl\ErrorFocus;

class ErrorFocus
{
	/**
	 * @param string[] $paths
	 * @param list<callable(
	 *     int $errno,
	 *     string $errstr,
	 *     string $errfile = ?,
	 *     int $errline = ?,
	 *     array $errcontext = ?
	 * ): bool|array{paths:string[], handler:callable(
	 *      int $errno,
	 *      string $errstr,
	 *      string $errfile = ?,
	 *      int $errline = ?,
	 *      array $errcontext = ?
	 *  ), paths_are: ['inclusive'|'exclusive']}> $additionalErrorHandlers
	 */
	public static function init($paths = [], $additionalErrorHandlers = [])
    {
        $list = new ErrorHandlerList();
        foreach ($paths as $path) {
            $path = realpath($path);
            if (false === $path) {
                // The path does not exist, so we'll ignore it
                continue;
            }
            $list->addErrorHandler(ErrorHandlerPath::fromString($path));
        }

        foreach ($additionalErrorHandlers as $handler) {
			if (is_array($handler)) {
				switch($handler['paths_are']) {
					case 'inclusive':
						$callback = ErrorHandlerCallback::callbackForPathStringArray(
							$handler['handler'],
							$handler['paths']
						);
						break;
					case 'exclusive':
						$callback = ErrorHandlerCallback::callbackOutsidePathStringArray(
							$handler['handler'],
							$handler['paths']
						);
						break;
					default:
						continue 2;
				}
				$list->addErrorHandler($callback);
				continue;
			}
            $list->addAdditionalErrorHandler($handler);
        }

        $previousHandler = set_error_handler($list);

		if (null !== $previousHandler) {
		    $list->addAdditionalErrorHandler($previousHandler);
		}
    }
}
