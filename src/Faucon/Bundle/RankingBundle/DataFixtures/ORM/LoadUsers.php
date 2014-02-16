<?php

namespace Faucon\Bundle\RankingBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Faucon\Bundle\ClubBundle\Entity\User;

class LoadUsers extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $container;
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager)
    {
        $usermanager = $this->container->get('fos_user.user_manager');

        $jfa = $this->createUser($usermanager, 'jfa', 'joachimfandersen@gmail.com', true);
        $manager->persist($jfa);
        
        $sw = $this->createUser($usermanager, 'sw', 'simon@wager.dk', false);
        $manager->persist($sw);
        
        $es = $this->createUser($usermanager, 'es', 'ebbe@sab.dk', false);
        $manager->persist($es);
        
        $al = $this->createUser($usermanager, 'al', 'alberto@leal.dk', false);
        $manager->persist($al);

        $kh = $this->createUser($usermanager, 'kh', 'kristian@hansen.dk', false);
        $manager->persist($kh);
        
        $pr = $this->createUser($usermanager, 'pr', 'per@rohmann.dk', false);
        $manager->persist($pr);

        $manager->flush();
    }
    
    public function getOrder()
    {
        return 1;
    }
    
    private function createUser($usermanager, $username, $email, $isadmin)
    {
        $user = $usermanager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEnabled(true);
        $user->setSuperAdmin($isadmin);
        $user->setPassword('test1234');
        return $user;
    }
}
