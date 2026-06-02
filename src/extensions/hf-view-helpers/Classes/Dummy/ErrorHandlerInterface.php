<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\Dummy;

interface ErrorHandlerInterface
{
    public function handle(\Exception $e): void;
}