<?php

namespace Faucon\Bundle\RankingBundle\Enums;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 'Enum' used to specify the status of a match - default is Played
 *
 * @author jfa
 */
class MatchStatus
{
    protected $container;
    
    const Played = 0;
    const Retired = 1;
    const Walkover = 2;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function getMatchStatuses()
    {
        $translator =$this->container->get('translator');
        return array(
            $translator->trans('match.status.played') => MatchStatus::Played,
            $translator->trans('match.status.retired') => MatchStatus::Retired,
            $translator->trans('match.status.walkover') => MatchStatus::Walkover
            );
    }
}
