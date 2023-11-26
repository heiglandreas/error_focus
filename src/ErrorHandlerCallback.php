<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 * 
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Org_Heigl\ErrorFocus;

use Org_Heigl\ErrorFocus\Exception\InvalidPath;
use Org_Heigl\ErrorFocus\Path\Path;
use Org_Heigl\ErrorFocus\Path\PathHandlerKind;
use Org_Heigl\ErrorFocus\Path\PathList;
use SplFileInfo;

final class ErrorHandlerCallback implements ErrorHandler
{
	private $paths;

	private $callback;

	private $kind;

	/**
	 * ErrorHandlerCallback constructor.
	 *
	 * @param callable(
	 *      int $errno,
	 *      string $errstr,
	 *      string= $errfile,
	 *      int= $errline,
	 *      array= $errcontext
	 *      ) $callback
	 */
	private function __construct(callable $callback, PathList $pathList, PathHandlerKind $kind)
	{
		$this->callback = $callback;
		$this->paths = $pathList;
		$this->kind = $kind;
	}

	public static function callbackForPathList(callable $callback, PathList $pathList): self
	{
		return new self($callback, $pathList, PathHandlerKind::PathIsIncluded());
	}

	public static function callbackForPathStringArray(callable $callback, array $pathList): self
	{
		$paths = PathList::empty();
		foreach ($pathList as $pathString) {
			try {
				$path = Path::fromString($pathString);
			} catch(InvalidPath $e) {}

			$paths = $paths->with($path);
		}

		return new self($callback, $paths, PathHandlerKind::PathIsIncluded());
	}

	public static function callbackOutsidePathList(callable $callback, PathList $pathList): self
	{
		return new self($callback, $pathList, PathHandlerKind::PathIsExcluded());
	}
	public static function callbackOutsidePathStringArray(callable $callback, array $pathList): self
	{
		$paths = PathList::empty();
		foreach ($pathList as $pathString) {
			try {
				$path = Path::fromString($pathString);
			} catch(InvalidPath $e) {}

			$paths = $paths->with($path);
		}

		return new self($callback, $paths, PathHandlerKind::PathIsExcluded());
	}

	public function __invoke($errno, $errstr, $errfile, $errline, array $errcontext = []): bool
	{
		$errorFile = new SplFileInfo($errfile);
		switch($this->kind) {
			case PathHandlerKind::PathIsIncluded():
				foreach ($this->paths as $path) {
					if (false === $path->isRootOf($errorFile)) {
						continue;
					}
					($this->callback)($errno, $errstr, $errline, $errcontext);
				}
				break;
			case PathHandlerKind::PathIsExcluded():
				$notIncluded = false;
				foreach ($this->paths as $path) {
					if (true === $path->isRootOf($errorFile)) {
						$notIncluded = true;
					}
				}
				if ($notIncluded === false) {
					($this->callback)($errno, $errstr, $errline, $errcontext);
				}
				break;
		}

		return false;
	}
}
