<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\API\Repository\Tests\FieldType;

use eZ\Publish\Core\FieldType\Image\Type;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;

/**
 * Image field type is not searchable in Legacy search engine, but will
 * be searchable with Solr search engine.
 *
 * This is implementation simply extends the original implementation in order to
 * define the field type as searchable, so that it can be tested.
 */
class SearchableImage extends Type
{
    public function isSearchable()
    {
        return true;
    }

    protected static function checkValueType($value)
    {
        $fieldTypeFQN = 'eZ\\Publish\\Core\\FieldType\\Image\\Value';
        $valueFQN = substr_replace($fieldTypeFQN, 'Value', strrpos($fieldTypeFQN, '\\') + 1);

        if (!$value instanceof $valueFQN) {
            throw new InvalidArgumentType('$value', $valueFQN, $value);
        }
    }
}
