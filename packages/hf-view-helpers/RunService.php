<?php declare(strict_types=1);

require_once __DIR__ . '../../../vendor/autoload.php';

use Hausformat\ViewHelpers\Dummy\ErrorHandler;
use Hausformat\ViewHelpers\Dummy\Service;

$service = new Service(new ErrorHandler());

$service->runDeprecateionFunc();

$service->doSomething();