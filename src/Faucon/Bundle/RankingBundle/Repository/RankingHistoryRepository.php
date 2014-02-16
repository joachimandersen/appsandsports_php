<?php

namespace Faucon\Bundle\RankingBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Faucon\Bundle\RankingBundle\Entity\Category;
use Faucon\Bundle\ClubBundle\Entity\User;
use Faucon\Bundle\RankingBundle\Entity\RankingHistory;
use Faucon\Bundle\RankingBundle\Entity\Ranking;

/**
 * RankingHistoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RankingHistoryRepository extends EntityRepository
{
    public function createHistoricRanking(User $user, Category $category, $oldranking)
    {
        if ($oldranking != null)
        {
            $rankinghistory = new RankingHistory();
            $rankinghistory->setRanking($oldranking);
            $rankinghistory->setOwner($user);
            $rankinghistory->setCategory($category);
            return $rankinghistory;
        }
        return null;
    }
    
    public function addHistoricRankings(Ranking $ranking, $oldranking, $newranking)
    {
        $rr = $this->getEntityManager()->getRepository('FauconRankingBundle:Ranking');
        $historicrankings = array();
        for ($i = $newranking; $i < $oldranking; $i++)
        {
            $user = $rr->getUserByRankAndCategory($i, $ranking->getCategory());
            $historicrankings[] = $this->createHistoricRanking($user, $ranking->getCategory(), $i);
        }
        $historicrankings[] = $this->createHistoricRanking($ranking->getPlayer(), $ranking->getCategory(), $oldranking);
        return $historicrankings;
    }
    
    public function getByUserAndCategory(User $user, Category $category)
    {
        return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('r')
                ->from('FauconRankingBundle:RankingHistory', 'r')
                ->where('r.owner = ?1')
                ->andWhere('r.category = ?2')
                ->setParameter(1, $user)
                ->setParameter(2, $category)
                ->orderBy('r.created')
                ->getQuery()
                ->getSingleResult();
    }
}