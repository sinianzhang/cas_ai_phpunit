<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\Dummy;

final readonly class Service implements ServiceInterface
{
    private ErrorHandler $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function doSomething(): void
    {
        try {
            echo "Do something...\n";
            throw new \Exception('Error in doing something!');
        } catch (\Exception $e) {
            $this->errorHandler->handle($e);
        }
    }

    public function runDeprecateionFunc(): bool
    {
        // PHPUnit test shows deprecation
        echo "Running deprecation function...\n";
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserSetting(
            'myCustomSetting',
            [],
            'after:emailMeAtLogin'
        );
        return true;
    }
}