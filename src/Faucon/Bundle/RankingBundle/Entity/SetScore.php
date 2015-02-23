<?php

namespace Faucon\Bundle\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Faucon\Bundle\RankingBundle\Entity\SetScore
 */
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="ranking_setscore")
 */
class SetScore
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $homegames;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $awaygames;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $number;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="\Faucon\Bundle\ClubBundle\Entity\User", inversedBy="setscores")
     * @ORM\JoinColumn(name="createdby_id", referencedColumnName="id", nullable=true)
     */
    private $createdby;

    /**
     * @ORM\ManyToOne(targetEntity="Score", inversedBy="setscores")
     */
    private $score;

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
     * Set homegames
     *
     * @param integer $homegames
     */
    public function setHomegames($homegames)
    {
        $this->homegames = $homegames;
    }

    /**
     * Get homegames
     *
     * @return integer 
     */
    public function getHomegames()
    {
        return $this->homegames;
    }

    /**
     * Set awaygames
     *
     * @param integer $awaygames
     */
    public function setAwaygames($awaygames)
    {
        $this->awaygames = $awaygames;
    }

    /**
     * Get awaygames
     *
     * @return integer 
     */
    public function getAwaygames()
    {
        return $this->awaygames;
    }

    /**
     * Set number
     *
     * @param integer $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
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
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * Set score
     *
     * @param Faucon\Bundle\RankingBundle\Entity\Score $score
     */
    public function setScore(\Faucon\Bundle\RankingBundle\Entity\Score $score)
    {
        $this->score = $score;
    }

    /**
     * Get score
     *
     * @return Faucon\Bundle\RankingBundle\Entity\Score 
     */
    public function getScore()
    {
        return $this->score;
    }
    
    public function __toString()
    {
        return $this->homegames.'/'.$this->awaygames;
    }
    
    public function getSetWinner()
    {
        return $this->homegames > $this->awaygames;
    }
}
