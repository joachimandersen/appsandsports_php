<?php

namespace Faucon\Bundle\RankingBundle\Enums;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 'Enum' used to specify the sport - default is Tennis
 *
 * @author jfa
 */
class Sport
{
    protected $container;
    
    const Tennis = 0;
    const TennisName = 'tennis';
    const Squash = 1;
    const SquashName = 'squash';
    const Badminton = 2;
    const BadmintonName = 'badminton';
    const TableTennis = 3;
    const TableTennisName = 'tabletennis';
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSports()
    {
        $translator =$this->container->get('translator');
        return array(
            Sport::Tennis => $translator->trans('sport.tennis'),
            Sport::Squash => $translator->trans('sport.squash'),
            Sport::Badminton => $translator->trans('sport.badminton'),
            Sport::TableTennis => $translator->trans('sport.tabletennis')
            );
    }
    
    public function getSport($id)
    {
        $sports = $this->getSports();
        return $sports[$id];
    }
    
    public function getSportNames()
    {
        return array(
            Sport::Tennis => Sport::TennisName,
            Sport::Squash => Sport::SquashName,
            Sport::Badminton => Sport::BadmintonName,
            Sport::TableTennis => Sport::TableTennisName
            );
    }

    public function getSportName($id)
    {
        $sportnames = $this->getSportNames();
        return $sportnames[$id];
    }

}
