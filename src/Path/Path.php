<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 * 
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Org_Heigl\ErrorFocus\Path;

use Org_Heigl\ErrorFocus\Exception\InvalidPath;
use SplFileInfo;

final class Path
{
	private $path;

	private function __construct(SplFileInfo $path)
	{
		$this->path = $path;
	}

	/**
	 * @throws InvalidPath when the path is not existend or is not a directory
	 */
	public static function fromString(string $path): self
	{
		$splPath = new SplFileInfo($path);
		if (false === $splPath->getRealPath()) {
			throw InvalidPath::doesNotExist($path);
		}

		if (! $splPath->isDir()) {
			throw InvalidPath::isNotDirectory($path);
		}

		return new self($splPath);
	}

	public function isRootOf(SplFileInfo $path): bool
	{
		return substr($path->getPAthName(), $this->path->getPathName()) === 0;
	}
}
