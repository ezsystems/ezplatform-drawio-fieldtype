<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformDrawIOFieldType\FieldType\DrawIO\Form;

use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\FormInterface;

final class FormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $fieldDefinition = $data->fieldDefinition;
        $formBuilder = $fieldForm->getConfig()->getFormFactory()->createBuilder();

        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($fieldForm->getConfig()->getOption('mainLanguageCode')) ?: reset($names);

        $fieldForm->add(
            $formBuilder->create(
                'value',
                FormType::class,
                [
                    'required' => $fieldDefinition->isRequired,
                    'label' => $label,
                ]
            )
            ->setAutoInitialize(false)
            ->getForm()
        );
    }
}
