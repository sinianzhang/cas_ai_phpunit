<?php

namespace Hausformat\Lib\Utility;

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

/**
 * Convert XML to Array
 *
 * ## Examples
 *
 *     $xml2array = new XmlToArrayUtility('<root><child>value</child></root>');
 *     $xml2array->convert();
 *     assert($xml2array->getArray() === ['root' => ['child' => 'value']]);
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class XmlToArrayUtility
{
    /**
     * @var \DOMXPath
     */
    protected $xpath;

    /**
     * @param string $xml
     */
    public function __construct(string $xml = '')
    {
        $document = new \DOMDocument();
        $document->loadXML($xml);
        $this->xpath = new \DOMXPath($document);
    }

    /**
     * @param string $xml
     * @param array $settings
     *
     * @return array
     */
    public static function convert($xml, $settings)
    {
        $converter = new self($xml);

        return $converter->doConvert($settings);
    }

    /**
     * @param array $settings
     * @param \DOMNode $element
     *
     * @return array
     */
    protected function doConvert(array $settings, $element = null)
    {
        $result = [];

        foreach ($settings as $key => $value) {
            if (is_string($value)) {
                $result[$key] = $this->xpath->evaluate($value, $element);
            } else {
                if (isset($value['query']) && isset($value['map'])) {
                    $subElements = $this->xpath->query($value['query'], $element);

                    foreach ($subElements as $subElement) {
                        $result[$key][] = $this->doConvert($value['map'], $subElement);
                    }
                } else {
                    throw new \InvalidArgumentException('Invalid configuration for ' . $key . ', requires \'query\' and \'map\'');
                }

            }
        }

        return $result;
    }
}
