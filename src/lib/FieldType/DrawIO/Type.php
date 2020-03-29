<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformDrawIOFieldType\FieldType\DrawIO;

use eZ\Publish\Core\FieldType\Image\Type as BaseType;

final class Type extends BaseType
{
    public const FIELD_TYPE_IDENTIFIER = 'ezdrawio';

    public function getFieldTypeIdentifier(): string
    {
        return self::FIELD_TYPE_IDENTIFIER;
    }

    protected function createValueFromInput($inputValue)
    {
        if (\is_string($inputValue)) {
            $inputValue = Value::fromString($inputValue);
        }

        if (\is_array($inputValue)) {
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

    public function fromHash($hash): Value
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($hash);
    }

    public function getEmptyValue(): Value
    {
        return new Value();
    }
}
