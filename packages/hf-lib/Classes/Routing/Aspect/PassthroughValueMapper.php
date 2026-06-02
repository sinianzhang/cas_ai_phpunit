<?php
declare(strict_types = 1);

namespace Hausformat\Lib\Routing\Aspect;

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

use TYPO3\CMS\Core\Routing\Aspect\StaticMappableAspectInterface;
use Hausformat\Lib\Utility\StringUtility;

/**
 * Very useful for e.g. pagination or static range like "2011 ... 2030" for years.
 *
 * Example:
 *   routeEnhancers:
 *     MyBlogPlugin:
 *       type: Extbase
 *       extension: BlogExample
 *       plugin: Pi1
 *       routes:
 *         - { routePath: '/list/{paging_widget}', _controller: 'BlogExample::list', _arguments: {'paging_widget': '@widget_0/currentPage'}}
 *       defaultController: 'BlogExample::list'
 *       requirements:
 *         paging_widget: '\d+'
 *       aspects:
 *         paging_widget:
 *           type: PassthroughValueMapper
 *
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class PassthroughValueMapper implements StaticMappableAspectInterface
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @var bool
     */
    protected $sanitizePath = false;

    /**
     * @var bool|mixed
     */
    protected $lowercase = false;

    /**
     * @param array $settings
     * @throws \InvalidArgumentException
     */
    public function __construct(array $settings = [])
    {
        $this->sanitizePath = $settings['sanitizePath'] ?? false;
        $this->lowercase = $settings['lowercase'] ?? false;

        if(!is_bool($this->sanitizePath)) {
            $this->sanitizePath = (boolean) $this->sanitizePath;
        }
        if(!is_bool($this->lowercase)) {
            $this->lowercase = (boolean) $this->lowercase;
        }

        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $value): ?string
    {
        if($this->sanitizePath) {
            $value = StringUtility::sanitizePath($value);
        }
        if($this->lowercase) {
            $value = strtolower($value);
        }

        $value = str_replace(' ', '_', $value);
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $value): ?string
    {
        return $value;
    }
}
