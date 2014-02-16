<?php

namespace Faucon\Bundle\RankingBundle\Listener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Faucon\Bundle\RankingBundle\Entity\Ranking;
use Faucon\Bundle\RankingBundle\Entity\RankingHistory;
use Symfony\Component\DependencyInjection\ContainerAware;

class RankingUpdater extends ContainerAware
{
    private function skipAutomaticRankingUpdater()
    {
        if ($this->container->hasScope('request')) {
            return false;
        }
        $request = $this->container->get('request');
        if ($request->attributes->get('skipevent') == 'true') {
            return true;
        }
        return false;
    }
    
    public function onFlush(OnFlushEventArgs $args)
    {
        if (!$this->skipAutomaticRankingUpdater()) {
            $em = $args->getEntityManager();
            $uow = $em->getUnitOfWork();
            foreach ($uow->getScheduledEntityUpdates() AS $entity)
            {
                if ($entity instanceof Ranking)
                {
                    $changeset = $uow->getEntityChangeSet($entity);
                    if (array_key_exists('ranking', $changeset))
                    {
                        $oldranking = $changeset['ranking'][0];
                        $newranking = $changeset['ranking'][1];
                        $rhr = $em->getRepository('FauconRankingBundle:RankingHistory');
                        $rankinghistories = $rhr->addHistoricRankings($entity, $oldranking, $newranking);
                        $rr = $em->getRepository('FauconRankingBundle:Ranking');
                        $rr->updateRankings($entity->getPlayer(), $entity->getCategory(), $newranking, $oldranking);
                        foreach($rankinghistories as $rankinghistory)
                        {
                            $em->persist($rankinghistory);
                            $class = $em->getClassMetadata('Faucon\Bundle\RankingBundle\Entity\RankingHistory');
                            $uow->computeChangeSet($class, $rankinghistory);
                        }
                    }
                }
            }
        }
    }
    
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity(); 
        if ($entity instanceof Ranking) { 
            $em = $args->getEntityManager();
            $rr = $em->getRepository('FauconRankingBundle:Ranking');
            $rr->updateRankings($entity->getPlayer(), $entity->getCategory(), null, $entity->getRanking());
        } 
    }
}

