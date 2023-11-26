<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 * 
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Org_Heigl\ErrorFocus\Path;

use Iterator;
use Org_Heigl\ErrorFocus\Path\Path;

/**
 * @Iterator<int, Path>
 */
final class PathList implements Iterator
{

	private $list = [];

	private function __construct(Path...$paths)
	{
		$this->list = $paths;
	}

	public static function fromPaths(Path ...$paths): self
	{
		return new self(...$paths);
	}

	public static function empty(): self
	{
		return new self();
	}

	public function with(Path $path) {
		return new self(...array_merge([$path], $this->list));
	}

    /**
     * @inheritDoc
     */
    public function current(): Path
    {
        return current($this->list);
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
		next($this->list);
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
		return key($this->list);
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
		return null !== $this->key();
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
		reset($this->list);
    }
}
