<?php

namespace Faucon\Bundle\RankingBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Faucon\Bundle\ClubBundle\Entity\User;
use Faucon\Bundle\RankingBundle\Entity\Ranking;

class LoadRankings extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $container;
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager)
    {
        $usermanager = $this->container->get('fos_user.user_manager');
        $this->addRanking($manager, $usermanager->findUserByUsernameOrEmail('jfa'), 6);
        $this->addRanking($manager, $usermanager->findUserByUsernameOrEmail('sw'), 3);
        $this->addRanking($manager, $usermanager->findUserByUsernameOrEmail('es'), 1);
        $this->addRanking($manager, $usermanager->findUserByUsernameOrEmail('al'), 4);
        $this->addRanking($manager, $usermanager->findUserByUsernameOrEmail('kh'), 2);
        $this->addRanking($manager, $usermanager->findUserByUsernameOrEmail('pr'), 5);

        $manager->flush();
    }
    
    public function getOrder()
    {
        return 4;
    }
    
    private function addRanking($manager, $user, $rank)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $ranking = new Ranking();
        $ranking->setCategory($em->getRepository('FauconRankingBundle:Category')->find(1));
        $ranking->setPlayer($user);
        $ranking->setRanking($rank);
        $ranking->setCreatedby($em->getRepository('FauconClubBundle:User')->find(2));
        $manager->persist($ranking);
    }
}
