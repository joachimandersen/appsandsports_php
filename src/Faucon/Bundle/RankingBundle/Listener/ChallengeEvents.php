<?php

namespace Faucon\Bundle\RankingBundle\Listener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Faucon\Bundle\RankingBundle\Entity\Challenge;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;

class ChallengeEvents
{
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity(); 
        if ($entity instanceof Challenge)
        { 
            $em = $args->getEntityManager();
            $cr = $em->getRepository('FauconRankingBundle:Challenge');
            if ($cr->hasOpenChallengeInCategory($entity->getChallenger(), $entity->getCategory())
                    || $cr->hasOpenChallengeInCategory($entity->getChallenged(), $entity->getCategory()))
            {
                throw new \DomainException('Only one active challenge is allowed per player');
            }
            else // send challenge mail
            {
                if ($this->container->getParameter('sendmails'))
                {
                    $this->sendChallengedMail($entity);
                    $this->sendChallengerMail($entity);
                }
            }
        } 
    }
    
    private function sendChallengedMail(\Faucon\Bundle\RankingBundle\Entity\Challenge $challenge)
    {
        $phone = $challenge->getChallenger()->getPhone();
        if ($phone == '') 
        {
            $phone = 'Intet nummer';
        }
        $message = \Swift_Message::newInstance()
            ->setSubject('Du er blevet udfordret')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($challenge->getChallenged()->getEmail())
            ->setBody($this->container->get('templating')->render(
                        'FauconRankingBundle:Challenge:challengeemail.txt.twig',
                        array(
                            'name' => $challenge->getChallenged(), 
                            'by' => $challenge->getChallenger(),
                            'phone' => $phone,
                            'email' => $challenge->getChallenger()->getEmail(),
                            'deadline' => new \DateTime(date("Y-m-d", strtotime("14 days"))))));
        try
        {
            $this->container->get('mailer')->send($message);
        }
        catch (Exception $ex)
        {
            // Log exception and message body
        }
    }
    
    private function sendChallengerMail(\Faucon\Bundle\RankingBundle\Entity\Challenge $challenge)
    {
        $phone = $challenge->getChallenged()->getPhone();
        if ($phone == '') 
        {
            $phone = 'Intet nummer';
        }
        $message = \Swift_Message::newInstance()
            ->setSubject('Du har oprette en udfordring')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($challenge->getChallenger()->getEmail())
            ->setBody($this->container->get('templating')->render(
                        'FauconRankingBundle:Challenge:challengeremail.txt.twig',
                        array(
                            'name' => $challenge->getChallenger(), 
                            'by' => $challenge->getChallenged(),
                            'phone' => $phone,
                            'deadline' => new \DateTime(date("Y-m-d", strtotime("14 days"))))));
        try
        {
            $this->container->get('mailer')->send($message);
        }
        catch (Exception $ex)
        {
            // Log exception and message body
        }
    }
}

?>
