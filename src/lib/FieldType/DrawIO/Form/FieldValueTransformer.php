<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformDrawIOFieldType\FieldType\DrawIO\Form;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;
use eZ\Publish\Core\IO\IOServiceInterface;
use EzSystems\EzPlatformDrawIOFieldType\FieldType\DrawIO\Value;
use Symfony\Component\Form\DataTransformerInterface;

final class FieldValueTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\Core\IO\IOServiceInterface */
    private $ioService;

    /** @var \eZ\Publish\API\Repository\FieldType */
    private $fieldType;

    public function __construct(IOServiceInterface $ioService, FieldType $fieldType)
    {
        $this->ioService = $ioService;
        $this->fieldType = $fieldType;
    }

    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        $hash = $this->fieldType->toHash($value);

        try {
            $dataUri = null;

            if (!empty($value->uri)) {
                $binary = $this->ioService->loadBinaryFileByUri($value->uri);

                $dataUri = 'data:';
                $dataUri .= $this->ioService->getMimeType($binary->id);
                $dataUri .= ';base64,';
                $dataUri .= base64_encode($this->ioService->getFileContents($binary));
            }

            $hash['data'] = $dataUri;
        } catch (InvalidArgumentValue | NotFoundException $e) {
            $hash['data'] = null;
        }

        return $hash;
    }

    public function reverseTransform($value)
    {
        if ($value === null || !$value['data']) {
            return $this->fieldType->getEmptyValue();
        }

        if (strpos($value['data'], 'data:') === 0) {
            $path = tempnam(sys_get_temp_dir(), 'diagram-') . '.png';

            $in = fopen($value['data'], 'r');
            $out = fopen($path, 'w');
            stream_copy_to_stream($in, $out);
            $value['inputUri'] = $path;
            $value['fileName'] = pathinfo($path, PATHINFO_BASENAME);
            $value['fileSize'] = filesize($path);
            fclose($in);
        }

        unset($value['data']);

        return $this->fieldType->fromHash($value);
    }
}
