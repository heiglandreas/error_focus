<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 * 
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Org_Heigl\ErrorFocus;

interface ErrorHandler
{
	public function __invoke($errno, $errstr, $errfile, $errline, array $errcontext = []): bool;
}
