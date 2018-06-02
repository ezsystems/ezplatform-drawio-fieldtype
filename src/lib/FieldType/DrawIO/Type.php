<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformDrawIOFieldType\FieldType\DrawIO;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Image\Type as BaseType;
use eZ\Publish\SPI\FieldType\Nameable;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use RuntimeException;

class Type extends BaseType implements Nameable
{
    const FIELD_TYPE_IDENTIFIER = 'ezdrawio';

    /**
     * {@inheritdoc}
     */
    public function getFieldTypeIdentifier()
    {
        return self::FIELD_TYPE_IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_string($inputValue)) {
            $inputValue = Value::fromString($inputValue);
        }

        if (is_array($inputValue)) {
            if (isset($inputValue['inputUri']) && file_exists($inputValue['inputUri'])) {
                $inputValue['fileSize'] = filesize($inputValue['inputUri']);
                if (!isset($inputValue['fileName'])) {
                    $inputValue['fileName'] = basename($inputValue['inputUri']);
                }
            }

            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * {@inheritdoc}
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($hash);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(SPIValue $value, FieldDefinition $fieldDefinition, $languageCode)
    {
        return $value->alternativeText;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(SPIValue $value)
    {
        throw new RuntimeException('Name generation provided via NameableField set via "ezpublish.fieldType.nameable" service tag');
    }
}
