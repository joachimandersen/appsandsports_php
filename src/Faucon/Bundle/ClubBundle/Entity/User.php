<?php

namespace Faucon\Bundle\ClubBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use Faucon\Bundle\ClubBundle\Entity\Club;

/**
 * Faucon\Bundle\ClubBundle\Entity\User
 */
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sport_club_user")
 * @ORM\Entity(repositoryClass="Faucon\Bundle\ClubBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
        $this->rankinghistory = new ArrayCollection();
        $this->userroles = new ArrayCollection();
        $this->rankings = new ArrayCollection();
        $this->gameaschallenged = new ArrayCollection();
        $this->gamesaschallenger = new ArrayCollection();
        $this->clubrelations = new ArrayCollection();
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Faucon\Bundle\RankingBundle\Entity\Ranking", mappedBy="user")
     */
    protected $rankings;

    /**
     * @ORM\OneToMany(targetEntity="Faucon\Bundle\RankingBundle\Entity\RankingHistory", mappedBy="user")
     */
    protected $rankinghistory;

    /**
     * @ORM\Column(type="string", length="255", nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length="255", nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length="255", nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="Faucon\Bundle\ClubBundle\Entity\ClubRelation", mappedBy="user")
     */
    protected $clubrelations;
    
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
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
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

    public function __toString()
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    /**
     * Set phone
     *
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Add clubrelation
     *
     * @param Faucon\Bundle\ClubBundle\Entity\ClubRelation $clubrelation
     */
    public function addClubRelation(\Faucon\Bundle\ClubBundle\Entity\ClubRelation $clubrelation)
    {
        $this->clubrelations[] = $clubrelation;
    }

    /**
     * Get clubrelations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getClubRelations()
    {
        return $this->clubrelations;
    }
}