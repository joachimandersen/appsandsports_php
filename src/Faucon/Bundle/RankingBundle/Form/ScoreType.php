<?php

namespace Faucon\Bundle\RankingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ScoreType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('created')
            ->add('createdby')
        ;
    }

    public function getName()
    {
        return 'faucon_bundle_rankingbundle_scoretype';
    }
}
