<?php

namespace Faucon\Bundle\RankingBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ScoreRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ScoreRepository extends EntityRepository
{
    public function saveScore($matchscore, \Faucon\Bundle\RankingBundle\Entity\Challenge $challenge, $user) {
        $score = new \Faucon\Bundle\RankingBundle\Entity\Score();
        $score->setCreatedby($user);
        $i = 1;
        foreach ($matchscore as $s) {
            $set = new \Faucon\Bundle\RankingBundle\Entity\SetScore();
            $set->setHomegames($s->home);
            $set->setAwaygames($s->away);
            $set->setNumber($i);
            $set->setCreatedby($user);
            $set->setScore($score);
            $score->addSetScore($set);
            $this->getEntityManager()->persist($set);
            $i++;
        }
        if ($challenge->getChallengerrank() > $challenge->getChallengedrank() && $this->homeWasWinner($matchscore)) {
            $challengerranking = $this->getEntityManager()
                    ->getRepository('FauconClubBundle:User')
                    ->getRankingInstanceByCategory($challenge->getChallenger(), $challenge->getCategory());
            $challengedranking = $this->getEntityManager()
                    ->getRepository('FauconClubBundle:User')
                    ->getRankingInstanceByCategory($challenge->getChallenged(), $challenge->getCategory());
            $challengerranking->setRanking($challengedranking->getRanking());
            $this->getEntityManager()->persist($challengerranking);
        }
        $this->getEntityManager()->persist($score);
        return $score;
    }
    
    public function isScoreValid($score)
    {
        $validlength = count($score) >= 3;
        if (!$validlength) {
            return false;
        }
        return $this->wasWinnerFound($score);
    }
    
    public function getWinner($score, \Faucon\Bundle\RankingBundle\Entity\Challenge $challenge)
    {
        if ($this->homeWasWinner($score)) {
            return $challenge->getChallenger();
        }
        return $challenge->getChallenged();
    }
    
    public function wasWinnerFound($score)
    {
        $home = 0;
        $away = 0;
        foreach ($score as $key => $set) {
            if (intval($set->home) > intval($set->away) && intval($set->home)-2 >= intval($set->away)) {
                $home++;
            }
            elseif (intval($set->away) > intval($set->home) && intval($set->away)-2 >= intval($set->home)) {
                $away++;
            }
            else {
                return false;
            }
        }
        return $home != $away && ($home == 3 || $away == 3) && ($home + $away < 6);
    }
    
    public function homeWasWinner($score)
    {
        $home = 0;
        $away = 0;
        foreach ($score as $set) {
            if (intval($set->home) > intval($set->away) && intval($set->home)-2 >= intval($set->away)) {
                $home++;
            }
            elseif (intval($set->away) > intval($set->home) && intval($set->away)-2 >= intval($set->home)) {
                $away++;
            }
            else {
                return false;
            }
        }
        return $home > $away;
    }
}