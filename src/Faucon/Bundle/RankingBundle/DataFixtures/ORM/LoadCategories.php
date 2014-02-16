<?php

namespace Faucon\Bundle\RankingBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Faucon\Bundle\RankingBundle\Entity\Category;

class LoadCategories extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $container;
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $category = new Category();
        $category->setClub($em->getRepository('FauconClubBundle:Club')->find(1));
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsernameOrEmail('jfa');
        $category->setCreatedby($user);
        $category->setName('Herre Senior');
        $category->setDescription('Rangliste for alle herre seniorspillere i Slagelse Squash Klub');
        $category->setSport(1);
        $manager->persist($category);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 3;
    }
}
