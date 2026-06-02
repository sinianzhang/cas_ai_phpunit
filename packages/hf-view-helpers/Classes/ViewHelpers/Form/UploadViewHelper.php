<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Form;

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

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Core\Crypto\HashService;
use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
 * Upload ViewHelper
 *
 * TODO: document this and figure out if it is still needed
 * / hf-extbase
 * @author .hausformat <entwicklung@hausformat.com>
 */
class UploadViewHelper extends AbstractFormFieldViewHelper
{
    /**
     * @var HashService
     *
     */
    protected $hashService;

    /**
     * @var PropertyMapper
     *
     */
    protected $propertyMapper;

    /**
     * @param HashService $hashService
     */
    public function injectHashService(HashService $hashService)
    {
        $this->hashService = $hashService;
    }

    /**
     * @param PropertyMapper $propertyMapper
     */
    public function injectPropertyMapper(PropertyMapper $propertyMapper)
    {
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * Initialize the arguments.
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('disableMultiUpload', 'boolean', '');
        $this->registerArgument('disabled', 'string', 'Specifies that the input element should be disabled when the page loads');
        $this->registerArgument('multiple', 'string', 'Specifies that the file input element should allow multiple selection of files');
        $this->registerArgument('accept', 'string', 'Specifies the allowed file extensions to upload via comma-separated list, example ".png,.gif"');
        $this->registerArgument('errorClass', 'string', 'CSS class to set if there are errors for this ViewHelper', false, 'f3-form-error');
    }

    /**
     * Render the upload field including possible resource pointer
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     * @api
     */
    public function render(): string
    {
        $resource = $this->getUploadedResource();

        if ($resource === null) {
            return $this->parentRender();
        }

        if (isset($this->arguments['multiple']) && $this->arguments['multiple']) {
            $output = $this->renderObjectStorage($resource);
        } else {
            $output = $this->renderFileReference($resource);
        }

        if(!isset($this->arguments['disableMultiUpload']) || $this->arguments['disableMultiUpload'] === false) {
            $output .= $this->parentRender();
        }

        return $output;
    }

    protected function parentRender() {
        $name = $this->getName();
        $allowedFields = ['name', 'type', 'tmp_name', 'error', 'size'];
        foreach ($allowedFields as $fieldName) {
            $this->registerFieldNameForFormTokenGeneration($name . '[' . $fieldName . ']');
        }
        $this->tag->addAttribute('type', 'file');

        if (isset($this->arguments['multiple'])) {
            $this->tag->addAttribute('name', $name . '[]');
        } else {
            $this->tag->addAttribute('name', $name);
        }

        $this->setErrorClassAttribute();
        return $this->tag->render();
    }

    /**
     * Return a previously uploaded resource.
     * Return NULL if errors occurred during property mapping for this property.
     *
     * @return FileReference|ObjectStorage|null
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     */
    protected function getUploadedResource(): ObjectStorage|FileReference|null
    {
        if ($this->getMappingResultsForProperty()->hasErrors()) {
            return null;
        }

        $resource = null;

        if ($this->respectSubmittedDataValue) {
            $resource = $this->getValueFromSubmittedFormData($resource);
        } elseif (isset($this->additionalArguments['value'])) {
            $resource = $this->additionalArguments['value'];
        } elseif ($this->isObjectAccessorMode()) {
            $resource = $this->getPropertyValue();
        }

        if ($resource instanceof FileReference) {
            return $resource;
        }

        if ($resource instanceof ObjectStorage) {
            return $resource;
        }

        return $this->propertyMapper->convert($resource, FileReference::class);
    }

    /**
     * @param ObjectStorage $objectStorage
     *
     * @return string
     */
    protected function renderObjectStorage(ObjectStorage $objectStorage): string
    {
        $output = '';

        /** @var FileReference $fileReference */
        foreach ($objectStorage as $fileReference) {
            $output .= $this->renderFileReference($fileReference);
        }

        return $output;
    }

    /**
     * @param FileReference $fileReference
     *
     * @return string
     */
    protected function renderFileReference(FileReference $fileReference): string
    {
        $output = '';

        $resourcePointerIdAttribute = '';

        if (isset($this->additionalArguments['id'])) {
            $resourcePointerIdAttribute = ' id="' . htmlspecialchars($this->additionalArguments['id']) . '-file-reference"';
        }

        $resourcePointerValue = $fileReference->getUid();

        if ($resourcePointerValue === null) {
            // Newly created file reference which is not persisted yet.
            // Use the file UID instead, but prefix it with "file:" to communicate this to the type converter
            $resourcePointerValue = 'file:' . $fileReference->getOriginalResource()->getOriginalFile()->getUid();
        }

        $name = $this->getName();

        if (isset($this->arguments['multiple'])) {
            $name .= '[]';
        }

        $input = '<input type="hidden" name="' . htmlspecialchars(
                $name
            ) . '[submittedFile][resourcePointer]" value="' . htmlspecialchars(
                $this->hashService->appendHmac((string)$resourcePointerValue, 'hfuploadvhsecretw0w')
            ) . '"' . $resourcePointerIdAttribute . ' />';

        $this->templateVariableContainer->add('resource', $fileReference);
        $this->templateVariableContainer->add('input', $input);
        $output .= $this->renderChildren();
        $this->templateVariableContainer->remove('resource');
        $this->templateVariableContainer->remove('input');

        return $output;
    }
}
