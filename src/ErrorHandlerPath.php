<?php

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org_Heigl\ErrorFocus;


use SplFileInfo;
use function error_get_last;
use function var_dump;

class ErrorHandlerPath implements ErrorHandler
{
    /** @var SplFileInfo */
    private $basePath;

    private function __construct(SplFileInfo $basePath)
    {
        $this->basePath = $basePath;
    }

    public static function fromString($basePath)
    {
        return self::fromSplFileInfo(new SplFileInfo(realpath($basePath)));
    }

    public static function fromSplFileInfo(SplFileInfo $basePath)
    {
        return new self($basePath);
    }

    /**
     * @param int $errno,
     * @param string $errstr,
     * @param string $errfile = ?,
     * @param int $errline = ?,
     * @param array $errcontext = ?
     */
    public function __invoke($errno, $errstr, $errfile, $errline, array $errcontext = []): bool
    {
        $errfile = realpath($errfile);
        if (false === $errfile) {
            return false;
        }

        if (strpos($errfile, $this->basePath->getRealPath()) === 0) {
            return true;
        }

        return false;
    }

}
