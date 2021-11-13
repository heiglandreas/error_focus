# Error-Focus

A package to stay focused on relevant entries in your error-log

## Scope

This package allows you to declare folders that are not under your development and that you
have therefore no way of fixing errors in. Errors or Warnings or notices that are raised within
those declared folders will not be logged to your error-log any more tso that you can concentrate
on the tasks that you can actually fix and don't have your error log fill up with stuff 
that others broke.

## Installation

The base installation is done via composer

```bash
$ composer require org_heigl/error_focus
```

After that you will have to set up the package in your bootstrap file.

```php

\Org_Heigl\ErrorFocus\ErrorFocus::init([
    __DIR__ . '/../vendor',
]);
```

That's it. Now every message from a file within your vendor-folder will not hit your 
error-log any more.

You can add more than one folder to this configuration.

**CAVEAT**: This will set an error-handler. If you need to set another error handler afterwards 
you will overwrite this one!

In that case you might want to use this slightly different setup:

```php
\Org_Heigl\ErrorFocus\ErrorFocus::init([
    __DIR__ . '/../vendor',
], [
    [$myErrorHandlerInstance, 'myErrorHandlerMethod'],
    $myInvocableInstance, 
    [MyStaticErrorHandler::class, 'myStaticErrorHandlerMethod'],
])
```

## Feedback

Please leave feedback on the issue-tracker at https://github.com/heiglandreas/error_focus