<?php

declare(strict_types=1);

namespace B13\SiteT3demo\ViewHelpers;

/*
 * This file is part of TYPO3 CMS-extension site_t3demo by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetSocialMediaPlatformFromUrlViewHelper
 *
 * View helper to return markup for social media icons based on external URL reference.
 */
class GetSocialMediaPlatformFromUrlViewHelper extends AbstractViewHelper
{
    /**
     * @var array
     */
    protected $socialMediaIdentifier = [
        'apple',
        'spotify',
        'deezer',
        'facebook',
        'instagram',
        'twitter',
        'linkedin',
        'google',
        'youtube',
        'gitlab',
        'typo3',
    ];

    /**
     * Initialize arguments.
     *
     * @api
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'url',
            'string',
            'external url to use as reference'
        );
    }

    /**
     * @return string the rendered string
     */
    public function render()
    {
        foreach ($this->socialMediaIdentifier as $name) {
            if (stripos($this->arguments['url'], $name)) {
                return $name;
            }
        }
        return '';
    }
}
