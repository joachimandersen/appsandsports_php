<?php

namespace Faucon\Bundle\RankingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

// Faucon\Bundle\RankingBundle\Entity\Category
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="ranking_challenge")
 * @ORM\Entity(repositoryClass="Faucon\Bundle\RankingBundle\Repository\ChallengeRepository")
 */
class Challenge
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Game", mappedBy="challenge")
     */
    protected $game;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Faucon\Bundle\ClubBundle\Entity\User", inversedBy="challengers")
     * @ORM\JoinColumn(name="challenger_id", referencedColumnName="id", nullable=true)
     */
    protected $challenger;

    /**
     * @ORM\ManyToOne(targetEntity="\Faucon\Bundle\ClubBundle\Entity\User", inversedBy="challengers")
     * @ORM\JoinColumn(name="challenged_id", referencedColumnName="id", nullable=true)
     */
    protected $challenged;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $challengerrank;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $challengedrank;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="challenges")
     */
    private $category;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="\Faucon\Bundle\ClubBundle\Entity\User", inversedBy="categories")
     * @ORM\JoinColumn(name="createdby_id", referencedColumnName="id", nullable=true)
     */
    protected $createdby;


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
     * Set game
     *
     * @param Faucon\Bundle\RankingBundle\Entity\Game $game
     */
    public function setGame(\Faucon\Bundle\RankingBundle\Entity\Game $game)
    {
        $this->game = $game;
    }

    /**
     * Get game
     *
     * @return Faucon\Bundle\RankingBundle\Entity\Game 
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set challenger
     *
     * @param Faucon\Bundle\ClubBundle\Entity\User $challenger
     */
    public function setChallenger(\Faucon\Bundle\ClubBundle\Entity\User $challenger)
    {
        $this->challenger = $challenger;
    }

    /**
     * Get challenger
     *
     * @return Faucon\Bundle\ClubBundle\Entity\User 
     */
    public function getChallenger()
    {
        return $this->challenger;
    }

    /**
     * Set challenged
     *
     * @param Faucon\Bundle\ClubBundle\Entity\User $challenged
     */
    public function setChallenged(\Faucon\Bundle\ClubBundle\Entity\User $challenged)
    {
        $this->challenged = $challenged;
    }

    /**
     * Get challenged
     *
     * @return Faucon\Bundle\ClubBundle\Entity\User 
     */
    public function getChallenged()
    {
        return $this->challenged;
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
        return $this->getChallenger().' VS '.$this->getChallenged();
    }

    /**
     * Set challengerrank
     *
     * @param integer $challengerrank
     */
    public function setChallengerrank($challengerrank)
    {
        $this->challengerrank = $challengerrank;
    }

    /**
     * Get challengerrank
     *
     * @return integer 
     */
    public function getChallengerrank()
    {
        return $this->challengerrank;
    }

    /**
     * Set challengedrank
     *
     * @param integer $challengedrank
     */
    public function setChallengedrank($challengedrank)
    {
        $this->challengedrank = $challengedrank;
    }

    /**
     * Get challengedrank
     *
     * @return integer 
     */
    public function getChallengedrank()
    {
        return $this->challengedrank;
    }

    /**
     * Set category
     *
     * @param Faucon\Bundle\RankingBundle\Entity\Category $category
     */
    public function setCategory(\Faucon\Bundle\RankingBundle\Entity\Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Faucon\Bundle\RankingBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
}