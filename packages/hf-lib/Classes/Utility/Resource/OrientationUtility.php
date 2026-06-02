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

use Hausformat\Lib\Domain\Type\Orientation;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileInterface;

/**
 * Orientation utility methods
 *
 * @group hf-lib / hf-image
 * @author .hausformat <entwicklung@hausformat.com>
 */
class OrientationUtility
{
    /** @internal */
    const CROP_PROPERTY = 'crop';

    /** @internal */
    const WIDTH_PROPERTY = 'width';

    /** @internal */
    const HEIGHT_PROPERTY = 'height';

    /**
     * Determines the orientation of a file based on its cropped area
     * if possible, or otherwise by its width/height.
     *
     * @param \TYPO3\CMS\Core\Resource\FileInterface $file
     * @param string|null $cropVariant
     *
     * @return \Hausformat\Lib\Domain\Type\Orientation
     */
    public static function getOrientation(FileInterface $file, ?string $cropVariant = null)
    {
        if ($file->hasProperty(self::CROP_PROPERTY) && isset($cropVariant)) {
            return self::getOrientationFromCrop($file, $cropVariant);
        } else {
            return self::getOrientationFromImageSize($file);
        }
    }

    private static function getOrientationFromCrop(FileInterface $file, string $cropVariantName): Orientation
    {
        $crop = $file->getProperty(self::CROP_PROPERTY);
        $cropVariantCollection = CropVariantCollection::create((string)$crop);
        $cropVariant = $cropVariantCollection->getCropArea($cropVariantName);
        $cropArea = $cropVariant->makeAbsoluteBasedOnFile($file)->asArray();

        return self::getOrientationFromSizes($cropArea['width'], $cropArea['height']);
    }

    private static function getOrientationFromSizes(int $width, int $height): Orientation
    {

        if ($width > $height) {
            $orientationValue = Orientation::LANDSCAPE;
        } elseif ($width < $height) {
            $orientationValue = Orientation::PORTRAIT;
        } else {
            $orientationValue = Orientation::SQUARE;
        }

        return $orientationValue;
    }

    private static function getOrientationFromImageSize(FileInterface $image): Orientation
    {
        $width = $image->getProperty(self::WIDTH_PROPERTY);
        $height = $image->getProperty(self::HEIGHT_PROPERTY);

        return self::getOrientationFromSizes((int)$width, (int)$height);
    }
}
