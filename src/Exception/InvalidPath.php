<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 * 
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Org_Heigl\ErrorFocus\Exception;

use RuntimeException;
final class InvalidPath extends RuntimeException
{

	public static function doesNotExist(string $path): self
	{
		return new self(sprintf('The path "%s" does not exist', $path));
	}

	public static function isNotDirectory(string $path): self
	{
		return new self(sprintf('The path "%s" is not a directory', $path));
	}
}
