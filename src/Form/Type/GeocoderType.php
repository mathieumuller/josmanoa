<?php

namespace App\Form\Type;

use App\Model\Coordinates;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class GeocoderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lat', Type\HiddenType::class, [
                'label' => false,
                'attr' => ['class' => 'latitude-input'],
            ])
            ->add('lng', Type\HiddenType::class, [
                'label' => false,
                'attr' => ['class' => 'longitude-input'],
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_merge(
            $view->vars,
            [
                'input_id' => $options['input_id'],
                'map_container_id' => $options['map_container_id'],
                'map_height' => $options['map_height'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, //Coordinates::class,
            'input_id' => 'pac-input',
            'map_container_id' => 'map',
            'map_height' => '400px',
        ]);
    }
}
