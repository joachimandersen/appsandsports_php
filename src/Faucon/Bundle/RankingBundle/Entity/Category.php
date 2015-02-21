<?php

namespace Faucon\Bundle\RankingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Faucon\Bundle\ClubBundle\Entity\Club;
use Faucon\Bundle\ClubBundle\Entity\User;

// Faucon\Bundle\RankingBundle\Entity\Category
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="ranking_category")
 * @ORM\Entity(repositoryClass="Faucon\Bundle\RankingBundle\Repository\CategoryRepository")
 */
class Category
{
    public function __construct()
    {
        $this->rankinghistory = new ArrayCollection();
        $this->rankings = new ArrayCollection();
        $this->games = new ArrayCollection();
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Ranking", mappedBy="category")
     */
    private $rankings;

    /**
     * @ORM\OneToMany(targetEntity="RankingHistory", mappedBy="category")
     */
    protected $rankinghistory;

    /**
     * @ORM\OneToMany(targetEntity="Game", mappedBy="category")
     */
    protected $games;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="Faucon\Bundle\ClubBundle\Entity\User", inversedBy="categories")
     * @ORM\JoinColumn(name="createdby_id", referencedColumnName="id", nullable=true)
     */
    private $createdby;

    /**
     * @ORM\ManyToOne(targetEntity="Faucon\Bundle\ClubBundle\Entity\Club", inversedBy="categories")
     */
    private $club;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sport;

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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
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
     * Add rankings
     *
     * @param Faucon\Bundle\RankingBundle\Entity\Ranking $rankings
     */
    public function addRanking(\Faucon\Bundle\RankingBundle\Entity\Ranking $rankings)
    {
        $this->rankings[] = $rankings;
    }

    /**
     * Get rankings
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRankings()
    {
        return $this->rankings;
    }

    /**
     * Add games
     *
     * @param Faucon\Bundle\RankingBundle\Entity\Game $games
     */
    public function addGame(\Faucon\Bundle\RankingBundle\Entity\Game $games)
    {
        $this->games[] = $games;
    }

    /**
     * Get games
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGames()
    {
        return $this->games;
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
        return $this->getName();
    }

    /**
     * Add rankinghistory
     *
     * @param Faucon\Bundle\RankingBundle\Entity\RankingHistory $rankinghistory
     */
    public function addRankingHistory(\Faucon\Bundle\RankingBundle\Entity\RankingHistory $rankinghistory)
    {
        $this->rankinghistory[] = $rankinghistory;
    }

    /**
     * Get rankinghistory
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRankinghistory()
    {
        return $this->rankinghistory;
    }

    /**
     * Set club
     *
     * @param Faucon\Bundle\ClubBundle\Entity\Club $club
     */
    public function setClub(\Faucon\Bundle\ClubBundle\Entity\Club $club)
    {
        $this->club = $club;
    }

    /**
     * Get club
     *
     * @return Faucon\Bundle\ClubBundle\Entity\Club 
     */
    public function getClub()
    {
        return $this->club;
    }
    
    /**
     * Set sport
     *
     * @param integer $sport
     */
    public function setSport($sport)
    {
        $this->sport = $sport;
    }

    /**
     * Get sport
     *
     * @return integer 
     */
    public function getSport()
    {
        return $this->sport;
    }
}
