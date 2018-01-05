<?php

namespace App\Form;

use App\Model\MediaUploadGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaUploadGroupType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('coordinates', Type\GeocoderType::class, [
                'label' => 'form.label.coordinates',
            ])
            ->add('dateFrom', Type\DatepickerType::class, [
                'label' => false,
            ])
            ->add('dateTo', Type\DatepickerType::class, [
                'label' => false,
            ])
            ->add('uploadId', BaseType\HiddenType::class)
            ->add(
                'albums',
                EntityType::class,
                [
                    'class' => 'App\\Entity\\Album',
                    'label' => 'form.label.album',
                    'multiple' => true,
                    'attr' => ['class' => 'select2'],
                ]
            )
            ->add('submit', BaseType\SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MediaUploadGroup::class,
        ]);
    }
}
