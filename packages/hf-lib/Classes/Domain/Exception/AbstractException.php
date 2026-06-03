<?php

namespace Hausformat\Lib\Domain\Exception;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\Exception;

/**
 * Interface AbstractExceptionInterface
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
interface AbstractExceptionInterface
{
}

/**
 * Abstract exception class for all domain errors that might occur in this extension.
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
abstract class AbstractException
    extends Exception
    implements AbstractExceptionInterface
{
}
