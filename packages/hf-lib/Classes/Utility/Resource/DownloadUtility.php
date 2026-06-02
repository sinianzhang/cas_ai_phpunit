<?php

namespace Hausformat\Lib\Utility\Resource;

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

use TYPO3\CMS\Core\Resource\FileInterface;

/**
 * Download utility methods
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class DownloadUtility
{
    /**
     * Makes the current request a download
     *
     * @param \TYPO3\CMS\Core\Resource\FileInterface $file
     * @param string $downloadName
     *
     * @return never-returns
     */
    public static function downloadFile(FileInterface $file, $downloadName = '')
    {
        if ($downloadName === '') {
            $downloadName = $file->getName();
        }

        $extension = '.' . $file->getExtension();

        if (strtolower(substr($downloadName, -1 * strlen($extension))) !== strtolower($extension)) {
            $downloadName .= $extension;
        }

        $localFile = $file->getForLocalProcessing(false);
        $fileSize = filesize($localFile);

        header('Content-Type: ' . $file->getMimeType());
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        readfile($localFile);

        die;
    }
}
