<?php

namespace Faucon\Bundle\ClubBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as RegistrationFormType;
use Symfony\Component\DependencyInjection\Container;

class RegistrateUserFormType extends RegistrationFormType
{
    protected $container;
    
    public function __construct($class, Container $container)
    {
        parent::__construct($class);
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('username');
        $builder->add('username', 'text', array(
            'label' =>
            $this->container->get('translator')->trans('profile.show.username')
        ));
        $builder->remove('email');
        $builder->add('email');
        $builder->remove('plainPassword');
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password', 
            'first_name' => $this->container->get('translator')->trans('profile.create.first.password'),
            'second_name' => $this->container->get('translator')->trans('profile.create.second.password')
            ));
        $builder->add('firstname', 'text', array(
            'label' =>
            $this->container->get('translator')->trans('profile.show.firstname')
        ));
        $builder->add('lastname', 'text', array(
            'label' =>
            $this->container->get('translator')->trans('profile.show.lastname')            
        ));
        $builder->add('phone', 'number', array(
            'label' =>
            $this->container->get('translator')->trans('profile.show.phone')
        ));
    }

    public function getName()
    {
        return 'club_user_register';
    }
}
