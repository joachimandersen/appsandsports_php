<?php

namespace Faucon\Bundle\RankingBundle\Tests\Repository;

use Faucon\Bundle\RankingBundle\Tests\AbstractTestCase;
use Faucon\Bundle\RankingBundle\Repository\RankingRepository;

class RankingRepositoryTest extends AbstractTestCase
{
    /*public function test1ConnectionUp()
    {
        $user = $this->entitymanager->getRepository('FauconRankingBundle:User')->find(1);
        $this->assertTrue($user->getId() == 1, 'User with Id = 1 was not found');
    }*/
    
    /**
     * @group Ranking
     */
    public function test2UpdateRankingsHigherRanking()
    {
        $ur = $this->entitymanager->getRepository('FauconClubBundle:User');
        $category = $this->entitymanager->getRepository('FauconRankingBundle:Category')->find(1);
        $user = $ur->find(1);
        $ranking = $ur->getRankingInstanceByCategory($user, $category);
        $this->assertTrue($ranking->getRanking() == 6, 'Chcek if initial rakning is as expected');
        $rr = $this->entitymanager->getRepository('FauconRankingBundle:Ranking');
        $ranking->setRanking(5);
        $this->entitymanager->persist($ranking);
        $this->entitymanager->flush();
        $number5 = $rr->getUserByRankAndCategory(5, $category);
        $this->assertTrue($number5->getId() == 1, 'Chcek that user with Id = 1 is ranked 5 now');
        $number6 = $rr->getUserByRankAndCategory(6, $category);
        $this->assertTrue($number6->getId() == 6, 'Chcek that user with Id = 6 is ranked 6 now');
    }
    
    /**
     * @group Ranking
     */
    public function test3UpdateRankingsLowerRanking()
    {
        $ur = $this->entitymanager->getRepository('FauconClubBundle:User');
        $category = $this->entitymanager->getRepository('FauconRankingBundle:Category')->find(1);
        $user = $ur->find(3);
        $ranking = $ur->getRankingInstanceByCategory($user, $category);
        $this->assertTrue($ranking->getRanking() == 1, 'Ranking should have been 1 was '.$ranking->getRanking());
        $rr = $this->entitymanager->getRepository('FauconRankingBundle:Ranking');
        $ranking->setRanking(3);
        $this->entitymanager->persist($ranking);
        $this->entitymanager->flush();
        $kristian = $rr->getUserByRankAndCategory(1, $category);
        $this->assertTrue($kristian->getId() == 5, 'Should have been kh was '.$kristian->getUsername());
        $simon = $rr->getUserByRankAndCategory(2, $category);
        $this->assertTrue($simon->getId() == 2, 'Should have been sw was '.$simon->getUsername());
        $ebbe = $rr->getUserByRankAndCategory(3, $category);
        $this->assertTrue($ebbe->getId() == 3, 'Should have been es was '.$ebbe->getUsername());
        $alberto = $rr->getUserByRankAndCategory(4, $category);
        $this->assertTrue($alberto->getId() == 4, 'Should have been al was '.$alberto->getUsername());
    }
    
    /**
     * @group Ranking
     */
    public function test4UpdateRankingsDeleteRanking()
    {
        $ur = $this->entitymanager->getRepository('FauconClubBundle:User');
        $category = $this->entitymanager->getRepository('FauconRankingBundle:Category')->find(1);
        $user = $ur->find(4);
        $ranking = $ur->getRankingInstanceByCategory($user, $category);
        $this->assertTrue($ranking->getRanking() == 4, 'Ranking should have been 4 was '.$ranking->getRanking());
        $this->entitymanager->remove($ranking);
        $this->entitymanager->flush();
        $rr = $this->entitymanager->getRepository('FauconRankingBundle:Ranking');
        $nouser = $rr->getUserByRankAndCategory(6, $category);
        $this->assertTrue($nouser == null, 'Should have been null was '.$nouser);
    }

    /**
     * @group Ranking
     */
    public function test5UpdateRankingsHigherRankingAddsToRankingHistory()
    {
        $ur = $this->entitymanager->getRepository('FauconClubBundle:User');
        $category = $this->entitymanager->getRepository('FauconRankingBundle:Category')->find(1);
        $user = $ur->find(1);
        $ranking = $ur->getRankingInstanceByCategory($user, $category);
        $this->assertTrue($ranking->getRanking() == 6, 'Chcek if initial rakning is as expected');
        $rr = $this->entitymanager->getRepository('FauconRankingBundle:Ranking');
        $ranking->setRanking(4);
        $this->entitymanager->persist($ranking);
        $this->entitymanager->flush();
        $number4 = $rr->getUserByRankAndCategory(4, $category);
        $this->assertTrue($number4->getId() == 1, 'Chcek that user with Id = 1 is ranked 4 now');
        $number6 = $rr->getUserByRankAndCategory(6, $category);
        $this->assertTrue($number6->getId() == 6, 'Chcek that user with Id = 6 is ranked 6 now');
        $number5 = $rr->getUserByRankAndCategory(5, $category);
        $this->assertTrue($number5->getId() == 4, 'Chcek that user with Id = 4 is ranked 5 now');
        $historicranking4 = $this->entitymanager->getRepository('FauconRankingBundle:RankingHistory')
                ->getByUserAndCategory($number4, $category);
        $this->assertTrue($historicranking4->getRanking() == 6, 'Check that historic ranking is 6 for user with Id = 1');
        $historicranking6 = $this->entitymanager->getRepository('FauconRankingBundle:RankingHistory')
                ->getByUserAndCategory($number6, $category);
        $this->assertTrue($historicranking6->getRanking() == 5, 'Check that historic ranking is 5 for user with Id = 6');
    }
    
    /**
     * @group Layer
     */
    public function test6GetLayerForRanking()
    {
        $rr = $this->entitymanager->getRepository('FauconRankingBundle:Ranking');
        $layer = $rr->getLayer(1);
        $this->assertTrue($layer == 1);
        $layer = $rr->getLayer(2);
        $this->assertTrue($layer == 2);
        $layer = $rr->getLayer(3);
        $this->assertTrue($layer == 2);
        $layer = $rr->getLayer(4);
        $this->assertTrue($layer == 3);
        $layer = $rr->getLayer(5);
        $this->assertTrue($layer == 3);
        $layer = $rr->getLayer(6);
        $this->assertTrue($layer == 3);
        $layer = $rr->getLayer(7);
        $this->assertTrue($layer == 4);
        $layer = $rr->getLayer(9);
        $this->assertTrue($layer == 4);
        $layer = $rr->getLayer(9);
        $this->assertTrue($layer == 4);
        $layer = $rr->getLayer(10);
        $this->assertTrue($layer == 4);
        $layer = $rr->getLayer(14);
        $this->assertTrue($layer == 5);
        $layer = $rr->getLayer(19);
        $this->assertTrue($layer == 6);
        $layer = $rr->getLayer(22);
        $this->assertTrue($layer == 7);
        $layer = $rr->getLayer(27);
        $this->assertTrue($layer == 7);
        $layer = $rr->getLayer(28);
        $this->assertEquals(8, $layer);
        $layer = $rr->getLayer(33);
        $this->assertEquals(8, $layer);
        $layer = $rr->getLayer(34);
        $this->assertEquals(9, $layer);
        $layer = $rr->getLayer(39);
        $this->assertEquals(9, $layer);
        $layer = $rr->getLayer(40);
        $this->assertEquals(10, $layer);
        $layer = $rr->getLayer(45);
        $this->assertEquals(10, $layer);
        $layer = $rr->getLayer(46);
        $this->assertEquals(11, $layer);
        $layer = $rr->getLayer(51);
        $this->assertEquals(11, $layer);
        $layer = $rr->getLayer(52);
        $this->assertEquals(12, $layer);
        $layer = $rr->getLayer(58);
        $this->assertEquals(13, $layer);
        $layer = $rr->getLayer(63);
        $this->assertEquals(13, $layer);
        $layer = $rr->getLayer(64);
        $this->assertEquals(14, $layer);
    }
        
    /**
     * @group Layer
     */
    public function test7GetPyramidArray()
    {
        $rr = $this->entitymanager->getRepository('FauconRankingBundle:Ranking');
        $rankings = array();
        for ($i=0; $i<39; $i++) {
            $rankings[] = array('ranking' => $i + 1);
        }
        $array = $rr->getPyramidArray($rankings);
        $this->assertEquals(9, count($array));
        $this->assertEquals(6, count(end($array)));
    }
}