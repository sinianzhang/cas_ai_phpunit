<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Variable;

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

use Hausformat\Lib\Utility\ObjectStorageUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Updates a property of an object variable.
 * The first part of the path argument is the name of the variable to update.
 *
 * ## Examples
 *
 *     <hf:variable.update path="settings.hf.company.name" value="newValue" />
 *     {settings.hf.company.name} == "newValue"
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class UpdateViewHelper extends AbstractViewHelper
{
    /**
     * @var string
     */
    protected static string $originalPath;

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function render(): void
    {
        $arguments = $this->arguments;
        if (!isset($arguments['path']) || trim($arguments['path']) === '') {
            throw new \InvalidArgumentException('argument \'path\' is mandatory');
        }

        if (!isset($arguments['value'])) {
            throw new \InvalidArgumentException('argument \'value\' is mandatory');
        }

        self::$originalPath = $arguments['path'];

        $path = $arguments['path'];
        $value = $arguments['value'];

        $container = $this->renderingContext->getVariableProvider();
        $pathArray = GeneralUtility::trimExplode('.', $path);
        $name = $pathArray[0];
        $exists = $container->exists($name);

        if (count($pathArray) === 1) {
            if ($exists) {
                $container->remove($name);
            }

            $container->add($path, $value);

            return;
        }

        if ($exists) {
            $existingObject = $container->get($name);
            $container->remove($name);
        } else {
            $existingObject = [];
        }

        $newObject = self::setOnDottedPath(array_slice($pathArray, 1), $existingObject, $value);

        $container->add($name, $newObject);
    }

    /**
     * @param array $path
     * @param mixed $object
     * @param mixed $value
     *
     * @return array|mixed
     * @throws \ReflectionException
     */
    protected static function setOnDottedPath(array $path, mixed $object, mixed $value): mixed
    {
        $currentProperty = $path[0];

        if (count($path) === 1) {
            return self::setValueOnKey($currentProperty, $object, $value);
        }

        $nextObject = ObjectStorageUtility::getValueFromDottedPath($currentProperty, $object);

        if ($nextObject === null) {
            $nextObject = [];
        }

        $newValue = self::setOnDottedPath(array_slice($path, 1), $nextObject, $value);

        return self::setValueOnKey($currentProperty, $object, $newValue);
    }

    /**
     * @param string $property
     * @param mixed  $object
     * @param mixed  $value
     *
     * @return array|mixed
     * @throws \ReflectionException
     */
    protected static function setValueOnKey(string $property, mixed $object, mixed $value): mixed
    {
        if (is_array($object)) {
            $object[$property] = $value;
        }

        if (is_object($object)) {
            $object = self::setValueInObject($property, $object, $value);
        }

        return $object;
    }

    /**
     * @param string $property
     * @param object $object
     * @param mixed  $value
     *
     * @return mixed
     * @throws \ReflectionException
     */
    protected static function setValueInObject(string $property, object $object, mixed $value): mixed
    {
        $setter = 'set' . ucfirst($property);

        if ($object instanceof ObjectStorage) {
            return self::attachToObjectStorage($object, $value);
        }

        if (trim($property) === '') {
            throw new \InvalidArgumentException('paramName can\'t be empty if object is not instance of ObjectStorage');
        }

        if ($object instanceof AbstractEntity) {
            return self::setPropertyInEntity($property, $object, $value);
        }

        if (is_callable([$object, $setter])) {
            $object->{$setter}($value);

            return $object;
        }

        return self::setPropertyInObject($property, $object, $value);
    }

    /**
     * @param ObjectStorage $objectStorage
     * @param object        $object
     *
     * @return mixed
     */
    protected static function attachToObjectStorage(ObjectStorage $objectStorage, object $object): mixed
    {
        $objectStorage->attach($object);

        return $objectStorage;
    }

    /**
     * @param string         $property
     * @param AbstractEntity $object
     * @param mixed          $value
     *
     * @return mixed
     */
    protected static function setPropertyInEntity(string $property, AbstractEntity $object, mixed $value): mixed
    {
        if ($object->_hasProperty($property)) {
            $object->_setProperty($property, $value);

            return $object;
        }

        throw new \InvalidArgumentException(
            'property ' . $property . ' not found on entity ' . (string)$object . ' on path ' . self::$originalPath
        );

    }

    /**
     * @param string $property
     * @param object $object
     * @param mixed  $value
     *
     * @return mixed
     * @throws \ReflectionException
     */
    protected static function setPropertyInObject(string $property, object $object, mixed $value): mixed
    {
        $rfo = new \ReflectionObject($object);

        if (!$rfo->hasProperty($property)) {
            throw new \InvalidArgumentException(
                'property ' . $property . ' not found on object ' . get_class($object) . ' on path ' . self::$originalPath
            );
        }

        $property = $rfo->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);

        return $object;
    }

    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('path', 'string',
                                'The Path to the Value to Update. First part is the variable name. Example: settings.hf.company.name',
                                true);
        $this->registerArgument('value', 'mixed', 'New value for defined path.', true);
    }
}
