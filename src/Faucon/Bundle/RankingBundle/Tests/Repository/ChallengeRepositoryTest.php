<?php

namespace Faucon\Bundle\RankingBundle\Tests\Repository;

use Faucon\Bundle\RankingBundle\Tests\AbstractTestCase;
use Faucon\Bundle\RankingBundle\Repository\ChallengeRepository;
use Faucon\Bundle\RankingBundle\Entity\Challenge;

class ChallengeRepositoryTest extends AbstractTestCase
{
    /**
     * @group Challenge
     */
    public function test1hasOpenChallengeInCategoryNoChallengesCreated()
    {
        $category = $this->entitymanager->getRepository('FauconRankingBundle:Category')->find(1);
        $user = $this->entitymanager->getRepository('FauconClubBundle:User')->find(1);
        $this->assertFalse(
                $this->entitymanager
                    ->getRepository('FauconRankingBundle:Challenge')
                    ->hasOpenChallengeInCategory($user, $category)
                );
    }
    
    /**
     * @group Challenge
     */
    public function test2hasOpenChallengeInCategoryOneChallengeCreated()
    {
        $category = $this->entitymanager->getRepository('FauconRankingBundle:Category')->find(1);
        $challenger = $this->entitymanager->getRepository('FauconClubBundle:User')->find(1);
        $this->createChallenge();
        $this->assertTrue(
                $this->entitymanager
                    ->getRepository('FauconRankingBundle:Challenge')
                    ->hasOpenChallengeInCategory($challenger, $category)
                );
    }
    
    /**
     * @group Challenge
     */
    public function test3UnableToSaveChallengeIfChallengerHasOpenChallenge()
    {
        $this->setExpectedException('DomainException');
        $this->createChallenge();
        $this->createChallenge();
    }
    
    /**
     * @group Challenge
     */
    public function test4UnableToSaveChallengeIfChallengedHasOpenChallenge()
    {
        $this->setExpectedException('DomainException');
        $this->createChallenge(2, 3);
        $this->createChallenge(1, 2);
    }
    
    /**
     * @group Challenge
     */
    public function test5CanSaveChallengeIfOldChallengeIsOlderThan14Days()
    {
        $challenge = $this->createChallenge(2, 3);
        $challenge->setCreated(new \DateTime(date("Y-m-d", strtotime("-14 days"))));
        $this->entitymanager->persist($challenge);
        $this->entitymanager->flush();
        $this->createChallenge(1, 2);
        // This assertion is only made if no exception is thrown by previous line
        $this->assertTrue(1===1);
    }
    
    private function createChallenge($challengerid = 1, $challengedid = 3)
    {
        $category = $this->entitymanager->getRepository('FauconRankingBundle:Category')->find(1);
        $challenger = $this->entitymanager->getRepository('FauconClubBundle:User')->find($challengerid);
        $challenged = $this->entitymanager->getRepository('FauconClubBundle:User')->find($challengedid);
        $challenge = new Challenge();
        $challenge->setChallenger($challenger);
        $challenge->setChallenged($challenged);
        $challenge->setCreatedby($challenger);
        $challenge->setChallengerrank(6);
        $challenge->setChallengedrank(3);
        $challenge->setCategory($category);
        $this->entitymanager->persist($challenge);
        $this->entitymanager->flush();
        return $challenge;
    }
}