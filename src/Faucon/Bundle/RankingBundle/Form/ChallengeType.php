<?php

namespace Faucon\Bundle\RankingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;

class ChallengeType extends AbstractType
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('challengerrank')
            ->add('challengedrank')
            ->add('created')
            ->add('game')
            ->add('category', 'entity', array(
                'class' =>
                'FauconRankingBundle:Category',
                'label' =>
                $this->container->get('translator')->trans('challenge.category.label')
            ))
            ->add('challenger', 'entity', array(
                'class' =>
                'FauconClubBundle:User',
                'label' =>
                $this->container->get('translator')->trans('challenge.challenger.label')
            ))
            ->add('challenged', 'entity', array(
                'class' =>
                'FauconClubBundle:User',
                'label' =>
                $this->container->get('translator')->trans('challenge.challenged.label')
            ))
            ->add('createdby')
        ;
    }

    public function getName()
    {
        return 'faucon_bundle_rankingbundle_challengetype';
    }
}
