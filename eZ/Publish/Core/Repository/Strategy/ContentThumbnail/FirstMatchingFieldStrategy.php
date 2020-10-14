<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\Strategy\ContentThumbnail;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Thumbnail;
use eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\Field\ThumbnailStrategy as ContentFieldThumbnailStrategy;
use eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;

final class FirstMatchingFieldStrategy implements ThumbnailStrategy
{
    /** @var \eZ\Publish\API\Repository\FieldTypeService */
    private $fieldTypeService;

    /** @var \eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\Field\ThumbnailStrategy */
    private $contentFieldStrategy;

    public function __construct(
        ContentFieldThumbnailStrategy $contentFieldStrategy,
        FieldTypeService $fieldTypeService
    ) {
        $this->contentFieldStrategy = $contentFieldStrategy;
        $this->fieldTypeService = $fieldTypeService;
    }

    public function getThumbnail(Content $content): ?Thumbnail
    {
        $fieldDefinitions = $content->getContentType()->getFieldDefinitions();
        foreach ($fieldDefinitions as $fieldDefinition) {
            if (!$fieldDefinition->isThumbnail) {
                continue;
            }

            $field = $content->getField($fieldDefinition->identifier);
            if ($field === null) {
                continue;
            }

            if (!$this->contentFieldStrategy->hasStrategy($field->fieldTypeIdentifier)) {
                continue;
            }

            $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);
            if (!$fieldType->isEmptyValue($field->value)) {
                return $this->contentFieldStrategy->getThumbnail($content, $fieldDefinition->identifier);
            }
        }

        return null;
    }
}
