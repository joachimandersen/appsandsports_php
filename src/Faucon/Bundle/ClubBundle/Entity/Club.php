<?php

namespace Faucon\Bundle\ClubBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Faucon\Bundle\ClubBundle\Entity\Club
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sport_club_club")
 * @ORM\Entity(repositoryClass="Faucon\Bundle\ClubBundle\Repository\ClubRepository")
 */
class Club
{
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->clubrelations = new ArrayCollection();
        $this->invitationtokens = new ArrayCollection();
    }
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $synopsis
     *
     * @ORM\Column(name="synopsis", type="string", length=4000)
     */
    private $synopsis;

    /**
     * @var string $street
     *
     * @ORM\Column(name="street", type="string", length=255)
     */
    private $street;

    /**
     * @var string $number
     *
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     */
    private $number;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string $zip
     *
     * @ORM\Column(name="zip", type="string", length=10)
     */
    private $zip;

    /**
     * @var string $region
     *
     * @ORM\Column(name="region", type="string", length=100)
     */
    private $region;

    /**
     * @var string $country
     *
     * @ORM\Column(name="country", type="string", length=100)
     */
    private $country;

    /**
     * @var string $latitude
     *
     * @ORM\Column(name="latitude", type="string", length=100)
     */
    private $latitude;

    /**
     * @var string $longitude
     *
     * @ORM\Column(name="longitude", type="string", length=100)
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity="Faucon\Bundle\RankingBundle\Entity\Category", mappedBy="club")
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="Faucon\Bundle\ClubBundle\Entity\ClubRelation", mappedBy="club")
     */
    protected $clubrelations;

    /**
     * @ORM\OneToMany(targetEntity="Faucon\Bundle\ClubBundle\Entity\InvitationToken", mappedBy="club")
     */
    protected $invitationtokens;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="Faucon\Bundle\ClubBundle\Entity\User", inversedBy="categories")
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
     * Set synopsis
     *
     * @param string $synopsis
     */
    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;
    }

    /**
     * Get synopsis
     *
     * @return string 
     */
    public function getSynopsis()
    {
        return $this->synopsis;
    }

    /**
     * Set street
     *
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set number
     *
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * Get zip
     *
     * @return string 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set region
     *
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * Get region
     *
     * @return string 
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country
     *
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * Get longitude
     *
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Add categories
     *
     * @param Faucon\Bundle\RankingBundle\Entity\Category $categories
     */
    public function addCategory(\Faucon\Bundle\RankingBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
    }

    /**
     * Get categories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
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

    /**
     * Add invitationtoken
     *
     * @param Faucon\Bundle\ClubBundle\Entity\InvitationToken $invitationtoken
     */
    public function addInvitationToken(\Faucon\Bundle\ClubBundle\Entity\InvitationToken $invitationtoken)
    {
        $this->invitationtokens[] = $invitationtoken;
    }

    /**
     * Get invitationtokens
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInvitationTokens()
    {
        return $this->invitationtokens;
    }
}