<?php

namespace Faucon\Bundle\RankingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;

class CategoryType extends AbstractType
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('sport', 'choice', array(
                'choices' => $this->container->get('sport')->getSports(),
                'required' => true
            ))
        ;
    }

    public function getName()
    {
        return 'faucon_bundle_rankingbundle_categorytype';
    }
}
