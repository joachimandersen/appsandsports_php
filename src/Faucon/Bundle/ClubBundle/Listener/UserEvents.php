<?php

namespace Faucon\Bundle\ClubBundle\Listener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Faucon\Bundle\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;

class UserEvents
{
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity(); 
        if ($entity instanceof User) {
            /*$em = $args->getEntityManager();
            foreach ($entity->getRankings() as $ranking) {
                $em->remove($ranking);
            }
            foreach ($entity->getClubRelations() as $clubrelation) {
                $em->remove($clubrelation);
            }
            foreach ($entity->getRankinghistory() as $rankinghistory) {
                $em->remove($rankinghistory);
            }
            $em->flush();*/
        }
    }
}

?>
