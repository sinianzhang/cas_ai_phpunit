<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\Dummy;

class ErrorHandler implements ErrorHandlerInterface
{
    /**
     * If the function returns void, a unit test is possible,
     * because the exception is catchable and the test will fail with a failed assertion instead of an error
     */
    public function handle(\Exception $e): void
    {
        echo $e->getMessage(). "\n";
    }
}