<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', Type\EmailType::class, [
                'required' => true,
                'label' => 'form.user.email.label',
                'attr' => [
                    'autofocus' => true,
                    'placeholder' => 'form.user.email.placeholder',
                ],
            ])
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                //'invalid_message' => 'form.user.password.validation.repeat_password',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => [
                    'label' => 'form.user.password.label',
                    'attr' => ['placeholder' => 'form.user.password.placeholder'],
                ],
                'second_options' => [
                    'label' => 'form.user.repeat_password.label',
                    'attr' => ['placeholder' => 'form.user.repeat_password.placeholder'],
                ],
            ])
            ->add('submit', Type\SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
