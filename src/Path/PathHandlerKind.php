<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 * 
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Org_Heigl\ErrorFocus\Path;

use RuntimeException;

final class PathHandlerKind
{
	private const PATH_IS_INCLUDED = 'inclusive';
	private const PATH_IS_EXCLUDED = 'exclusive';

	private const STATES = [
		self::PATH_IS_INCLUDED,
		self::PATH_IS_EXCLUDED,
	];

	private $state;

	private static $instances = [
		self::PATH_IS_INCLUDED => null,
		self::PATH_IS_EXCLUDED => null,
	];

	private function __construct(string $state)
	{
		if (! in_array($state, self::STATES)) {
			throw new RuntimeException(sprintf(
				'The PathParameterKind "%s" is not valid',
				$state
			));
		}

		$this->state = $state;
	}

	public function getName(): string
	{
		 switch($this->state) {
			 case self::PATH_IS_INCLUDED:
				 return 'PATH_IS_INCLUDED';
			 case self::PATH_IS_EXCLUDED:
				 return 'PATH_IS_EXCLUDED';
			 default:
				throw new RuntimeException('This should never happen!');
		}
	}

	public static function PathIsIncluded(): self
	{
		if (null === self::$instances[self::PATH_IS_INCLUDED]) {
			self::$instances[self::PATH_IS_INCLUDED] = new self(self::PATH_IS_INCLUDED);
		}

		return self::$instances[self::PATH_IS_INCLUDED];
	}

	public static function PathIsExcluded(): self
	{
		if (null === self::$instances[self::PATH_IS_EXCLUDED]) {
			self::$instances[self::PATH_IS_EXCLUDED] = new self(self::PATH_IS_EXCLUDED);
		}

		return self::$instances[self::PATH_IS_EXCLUDED];
	}
}
