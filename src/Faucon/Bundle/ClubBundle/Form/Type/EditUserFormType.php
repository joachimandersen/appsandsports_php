<?php

namespace Faucon\Bundle\ClubBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\ProfileFormType as ProfileFormType;
use Symfony\Component\DependencyInjection\Container;

class EditUserFormType extends ProfileFormType
{
    protected $container;
    
    public function __construct($class, Container $container)
    {
        parent::__construct($class);
        $this->container = $container;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('current');
        $builder->add('current', 'password', 
            array(
                'label' => 
                $this->container->get('translator')->trans('profile.edit.confirm.password')
            ));
    }
    
    public function buildUserForm(FormBuilder $builder, array $options)
    {
        parent::buildUserForm($builder, $options);
        $builder->remove('username');
        $builder->add('username', 'text', array(
            'label' =>
            $this->container->get('translator')->trans('profile.show.username')
        ));
        $builder->remove('email');
        $builder->add('email');
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
        return 'club_user_edit';
    }
}