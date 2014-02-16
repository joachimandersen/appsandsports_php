<?php

namespace Faucon\Bundle\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Faucon\Bundle\RankingBundle\Entity\Game
 */
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="ranking_game")
 * @ORM\Entity(repositoryClass="Faucon\Bundle\RankingBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length="4000", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $played;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created;

    /**
     * @ORM\OneToOne(targetEntity="Challenge", inversedBy="game")
     */
    private $challenge;

    /**
     * @ORM\ManyToOne(targetEntity="\Faucon\Bundle\ClubBundle\Entity\User", inversedBy="games")
     * @ORM\JoinColumn(name="createdby_id", referencedColumnName="id", nullable=true)
     */
    private $createdby;

    /**
     * @ORM\OneToOne(targetEntity="Score")
     */
    private $score;

    /**
     * @ORM\ManyToOne(targetEntity="\Faucon\Bundle\ClubBundle\Entity\User", inversedBy="games")
     */
    private $winner;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $notfinished;
    
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
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set played
     *
     * @param datetime $played
     */
    public function setPlayed($played)
    {
        $this->played = $played;
    }

    /**
     * Get played
     *
     * @return datetime 
     */
    public function getPlayed()
    {
        return $this->played;
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

    /**
     * Set winner
     *
     * @param Faucon\Bundle\ClubBundle\Entity\User $winner
     */
    public function setWinner(\Faucon\Bundle\ClubBundle\Entity\User $winner)
    {
        $this->winner = $winner;
    }

    /**
     * Get winner
     *
     * @return Faucon\Bundle\ClubBundle\Entity\User 
     */
    public function getWinner()
    {
        return $this->winner;
    }
    
    /**
     * @ORM\prePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /*public function __toString()
    {
        return $this->getPlayer().' ('.$this->getRanking().')';
    }*/

    /**
     * Set challenge
     *
     * @param Faucon\Bundle\RankingBundle\Entity\Challenge $challenge
     */
    public function setChallenge(\Faucon\Bundle\RankingBundle\Entity\Challenge $challenge)
    {
        $this->challenge = $challenge;
    }

    /**
     * Get challenge
     *
     * @return Faucon\Bundle\RankingBundle\Entity\Challenge 
     */
    public function getChallenge()
    {
        return $this->challenge;
    }

    public function __toString()
    {
        return $this->getScore()->__toString();
    }

    /**
     * Set notfinished
     *
     * @param integer $notfinished
     */
    public function setNotfinished($notfinished)
    {
        $this->notfinished = $notfinished;
    }

    /**
     * Get notfinished
     *
     * @return integer 
     */
    public function getNotfinished()
    {
        return $this->notfinished;
    }
}