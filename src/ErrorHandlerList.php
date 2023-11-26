<?php

namespace Org_Heigl\ErrorFocus;

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */
class ErrorHandlerList
{
    private $list = [];

    private $furtherHandlers = [];

    public function addErrorHandler(ErrorHandler $handler)
    {
        $this->list[] = $handler;
    }

    public function addAdditionalErrorHandler(callable $handler)
    {
        $this->furtherHandlers[] = $handler;
    }

    /**
     * @param int $errno,
     * @param string $errstr,
     * @param string $errfile = ?,
     * @param int $errline = ?,
     * @param array $errcontext = ?
     */
    public function __invoke($errno, $errstr, $errfile, $errline, array $errcontext = [])
    {
		$returnValue = false;
        foreach ($this->list as $handler) {
			if ($handler instanceof ErrorHandlerCallback) {
				$handler($errno, $errstr, $errfile, $errline, $errcontext);
				continue;
			}
            if (true === $handler($errno, $errstr, $errfile, $errline, $errcontext)) {
                $returnValue = true;
            }
        }

        foreach ($this->furtherHandlers as $handler) {
            $handler($errno, $errstr, $errfile, $errline, $errcontext);
        }

        return $returnValue;
    }}
