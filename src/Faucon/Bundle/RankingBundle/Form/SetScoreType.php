<?php

namespace Faucon\Bundle\RankingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SetScoreType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('homegames')
            ->add('awaygames')
            ->add('number')
            ->add('created')
            ->add('createdby')
            ->add('score')
        ;
    }

    public function getName()
    {
        return 'faucon_bundle_rankingbundle_setscoretype';
    }
}
