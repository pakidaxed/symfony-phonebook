<?php

namespace App\Form;

use App\Entity\TempContacts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TempContactsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Adding spec. type here, because entity saves the receiver as INT user id
        $builder
            ->add('receiver', EmailType::class)
            ->add('comment')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TempContacts::class,
        ]);
    }
}
