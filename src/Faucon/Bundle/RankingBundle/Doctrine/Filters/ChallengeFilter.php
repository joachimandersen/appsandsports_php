<?php

namespace Example;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class ChallengeFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        return $targetTableAlias.'.created > '.new \DateTime(date("Y-m-d", strtotime("-14 days")));
    }
}