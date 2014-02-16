<?php

namespace Faucon\Bundle\RankingBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Faucon\Bundle\ClubBundle\Entity\Club;

class LoadClubs extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $container;
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsernameOrEmail('jfa');
        $club = new Club();
        $club->setCity('Odense');
        $club->setCountry('Denmark');
        $club->setCreatedby($user);
        $club->setLatitude('45');
        $club->setLongitude('45');
        $club->setName('Club name');
        $club->setNumber('3');
        $club->setRegion('Fyn');
        $club->setStreet('Vejen');
        $club->setSynopsis('fd');
        $club->setZip('5000');
        $manager->persist($club);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 2;
    }
}
