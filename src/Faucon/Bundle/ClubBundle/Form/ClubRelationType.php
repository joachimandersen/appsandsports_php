<?php

namespace Faucon\Bundle\ClubBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;

class ClubRelationType extends AbstractType
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('club', new \Faucon\Bundle\ClubBundle\Form\ClubType(), array('data_class' => 'Faucon\Bundle\ClubBundle\Entity\Club'))
                ->add('user', $this->container->get('faucon_user.create.form.type'));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Faucon\Bundle\ClubBundle\Entity\ClubRelation',
        );
    }

    public function getName()
    {
        return 'faucon_bundle_clubbundle_clubrelationtype';
    }
}
