<?php

namespace Faucon\Bundle\RankingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Faucon\Bundle\RankingBundle\Entity\Score
 */
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="ranking_score")
 * @ORM\Entity(repositoryClass="Faucon\Bundle\RankingBundle\Repository\ScoreRepository")
 */
class Score
{
    public function __construct()
    {
        $this->setscore = new ArrayCollection();
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="SetScore", mappedBy="score")
     */
    private $setscore;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="\Faucon\Bundle\ClubBundle\Entity\User", inversedBy="scores")
     * @ORM\JoinColumn(name="createdby_id", referencedColumnName="id", nullable=true)
     */
    private $createdby;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add setscore
     *
     * @param Faucon\Bundle\RankingBundle\Entity\SetScore $setscore
     */
    public function addSetScore(\Faucon\Bundle\RankingBundle\Entity\SetScore $setscore)
    {
        $this->setscore[] = $setscore;
    }

    /**
     * Get setscore
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSetscore()
    {
        return $this->setscore;
    }

    /**
     * Set createdby
     *
     * @param Faucon\Bundle\ClubBundle\Entity\User $createdby
     */
    public function setCreatedby(\Faucon\Bundle\ClubBundle\Entity\User $createdby)
    {
        $this->createdby = $createdby;
    }

    /**
     * Get createdby
     *
     * @return Faucon\Bundle\ClubBundle\Entity\User 
     */
    public function getCreatedby()
    {
        return $this->createdby;
    }

    /**
     * @ORM\prePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }
    
    public function __toString()
    {
        $result = '';
        foreach ($this->setscore as $set) {
            if ($result != '')
            {
                $result .= ' - ';
            }
            $result .= $set;
        }
        return $result;
    }
}