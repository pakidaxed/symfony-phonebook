<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // There is a problem! if not showing label, then not showing error too
        // So now the error shows, but the label is set to null, just not to show and SR won't see too
        $builder
            ->add('email', EmailType::class, ['label' => false, 'attr' => ['placeholder' => '*Email address']])
            ->add('full_name', null, ['label' => false, 'attr' => ['placeholder' => '*Full name']])
            ->add('phone', null, ['label' => false, 'attr' => ['placeholder' => 'Phone number']])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => ['label' => false, 'attr' => ['placeholder' => 'Password']],
                'second_options' => ['label' => false, 'attr' => ['placeholder' => 'Confirm Password']]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}