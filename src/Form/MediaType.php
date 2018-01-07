<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', BaseType\TextType::class, [
                'label' => 'form.media.name',
            ])
            ->add('description', BaseType\TextareaType::class, [
                'label' => 'form.media.description',
            ])
            ->add('coordinates', Type\GeocoderType::class, [
                'label' => 'form.coordinates',
            ])
            ->add('date', Type\DatepickerType::class, [
                'label' => 'form.media.date',
            ])
            ->add(
                'albums',
                EntityType::class,
                [
                    'class' => 'App\\Entity\\Album',
                    'label' => 'form.media.albums',
                    'multiple' => true,
                    'attr' => ['class' => 'select2'],
                    'by_reference' => false,
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
            'data_class' => Media::class,
        ]);
    }
}
