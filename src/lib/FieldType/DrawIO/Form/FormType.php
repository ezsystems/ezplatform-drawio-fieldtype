<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformDrawIOFieldType\FieldType\DrawIO\Form;

use EzSystems\EzPlatformDrawIOFieldType\FieldType\DrawIO\Type as FieldType;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\IO\IOServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormType extends AbstractType
{
    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    /**
     * @var \eZ\Publish\Core\IO\IOServiceInterface
     */
    private $ioService;

    /**
     * FormType constructor.
     *
     * @param \eZ\Publish\API\Repository\FieldTypeService $fieldTypeService
     * @param \eZ\Publish\Core\IO\IOServiceInterface $ioService
     */
    public function __construct(FieldTypeService $fieldTypeService, IOServiceInterface $ioService)
    {
        $this->fieldTypeService = $fieldTypeService;
        $this->ioService = $ioService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'data',
                HiddenType::class,
                [
                    'required' => $options['required'],
                ]
            )
            ->add(
                'alternativeText',
                TextType::class,
                [
                    'label' => /** @Desc("Alternative text") */ 'content.field_type.ezdrawio.alternative_text',
                ]
            );

        $builder->addModelTransformer(new FieldValueTransformer(
            $this->ioService,
            $this->fieldTypeService->getFieldType(FieldType::FIELD_TYPE_IDENTIFIER)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'ezdrawio',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_' . FieldType::FIELD_TYPE_IDENTIFIER;
    }
}
