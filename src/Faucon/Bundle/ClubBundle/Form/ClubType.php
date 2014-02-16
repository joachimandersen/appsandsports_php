<?php

namespace Faucon\Bundle\ClubBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ClubType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('synopsis','textarea')
            ->add('street', 'hidden')
            ->add('number', 'hidden')
            ->add('city', 'hidden')
            ->add('zip', 'hidden')
            ->add('region', 'hidden')
            ->add('country', 'hidden')
            ->add('longitude', 'hidden')
            ->add('latitude', 'hidden')
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Faucon\Bundle\ClubBundle\Entity\Club',
        );
    }

    public function getName()
    {
        return 'faucon_bundle_rankingbundle_clubtype';
    }
}
