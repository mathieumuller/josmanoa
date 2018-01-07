<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Security\AjaxAuthenticator;

class LoginType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', Type\EmailType::class, [
                'label' => 'form.login.username.label',
                'data' => $options['last_username'],
                'attr' => [
                    'placeholder' => 'form.login.username.placeholder',
                ],
                'block_name' => '_username',
            ])
            ->add('_password', Type\PasswordType::class, [
                'label' => 'form.login.password.label',
                'attr' => [
                    'placeholder' => 'form.login.password.placeholder',
                ],
                'block_name' => '_password',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'last_username' => null,
            'attr' => ['id' => 'form-login'],
            'csrf_token_id' => AjaxAuthenticator::CSRF_LOGIN_ID,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
