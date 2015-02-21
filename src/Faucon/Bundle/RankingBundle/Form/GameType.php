<?php

namespace Faucon\Bundle\RankingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'textarea', array('required' => true))
            ->add('played', 'date', array('widget' => 'single_text', 'attr' => array('class' => 'js_date'), 'format' => 'dd-MM-yyyy' , 'required' => true))
            ->add('created')
            ->add('challenge')
            ->add('createdby')
            ->add('score')
            ->add('notfinished')
            ->add('winner')
        ;
    }

    public function getName()
    {
        return 'faucon_bundle_rankingbundle_gametype';
    }
}
