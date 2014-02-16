<?php

namespace Faucon\Bundle\RankingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RankingHistoryType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('ranking')
            ->add('created')
            ->add('owner')
        ;
    }

    public function getName()
    {
        return 'faucon_bundle_rankingbundle_rankinghistorytype';
    }
}
