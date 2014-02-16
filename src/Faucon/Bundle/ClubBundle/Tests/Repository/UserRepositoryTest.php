<?php

namespace Faucon\Bundle\ClubBundle\Tests\Repository;

use Faucon\Bundle\RankingBundle\Tests\AbstractTestCase;
use Faucon\Bundle\ClubBundle\Repository\UserRepository;

class UserRepositoryTest extends AbstractTestCase
{
    
    /**
     * @group User
     */
    public function test1DeleteUserWithRanking()
    {
        $ur = $this->entitymanager->getRepository('FauconClubBundle:User');
        $user = $this->entitymanager->createQueryBuilder()
                ->select('u')
                ->from('FauconClubBundle:User', 'u')
                ->where('u.id = 1')
                ->getQuery()
                ->getSingleResult();
        $ranking = $this->entitymanager->createQueryBuilder()
                ->select('r')
                ->from('FauconRankingBundle:Ranking', 'r')
                ->join('r.player', 'p')
                ->where('p.id = ?1')
                ->setParameter(1, 1)
                ->getQuery()
                ->getOneOrNullResult();
        $this->assertTrue($user != null, 'Expect that the user is in database');
        $this->assertTrue($ranking != null, 'Expect that the ranking is in the database');
        $ur->deleteUser($user);
        $ranking = $this->entitymanager->createQueryBuilder()
                ->select('r')
                ->from('FauconRankingBundle:Ranking', 'r')
                ->join('r.player', 'p')
                ->where('p.id = 1')
                ->getQuery()
                ->getOneOrNullResult();
        $user = $this->entitymanager->createQueryBuilder()
                ->select('u')
                ->from('FauconClubBundle:User', 'u')
                ->where('u.id = 1')
                ->getQuery()
                ->getOneOrNullResult();
        $this->assertTrue($user == null, 'Expect that the user has been deleted');
        $this->assertTrue($ranking == null, 'Expect that the ranking has been deleted');
    }
    
    /**
     * @group NewUser
     */
    public function test2IsUsernameAvailable()
    {
        $ur = $this->entitymanager->getRepository('FauconClubBundle:User');
        $this->assertTrue($ur->isUsernameAvailable('jf'));
        $this->assertFalse($ur->isUsernameAvailable('jfa'));
    }

    /**
     * @group NewUser
     */
    public function test3IsEmailAddressAvailable()
    {
        $ur = $this->entitymanager->getRepository('FauconClubBundle:User');
        $this->assertTrue($ur->isEmailAddressAvailable('jfa@netmester.dk'));
        $this->assertFalse($ur->isEmailAddressAvailable('joachimfandersen@gmail.com'));        
    }
}