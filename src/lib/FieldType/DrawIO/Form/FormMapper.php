<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformDrawIOFieldType\FieldType\DrawIO\Form;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\FormInterface;

class FormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
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
