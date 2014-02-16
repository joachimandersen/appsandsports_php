<?php

namespace Faucon\Bundle\ClubBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('phone')
        ;
    }

    public function getName()
    {
        return 'faucon_bundle_clubbundle_usertype';
    }
}
