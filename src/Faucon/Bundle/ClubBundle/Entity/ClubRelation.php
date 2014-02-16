<?php

namespace Faucon\Bundle\ClubBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Faucon\Bundle\ClubBundle\Entity\ClubRelation
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sport_club_club_relation")
 * @ORM\Entity(repositoryClass="Faucon\Bundle\ClubBundle\Repository\ClubRelationRepository")
 */
class ClubRelation
{
    public function __construct()
    {
        $this->hasLicense = false;
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
     * @ORM\ManyToOne(targetEntity="Faucon\Bundle\ClubBundle\Entity\User", inversedBy="clubrelation")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Faucon\Bundle\ClubBundle\Entity\Club", inversedBy="clubrelation")
     */
    protected $club;
    
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isAdmin;
    
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $hasLicense;

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
     * Set hasLicense
     *
     * @param boolean $hasLicense
     */
    public function setHasLicense($hasLicense)
    {
        $this->hasLicense = $hasLicense;
    }

    /**
     * Get hasLicense
     *
     * @return boolean
     */
    public function getHasLicense()
    {
        return $this->hasLicense;
    }
    
    /**
     * Set isAdmin
     *
     * @param boolean $isAdmin
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * Get isAdmin
     *
     * @return boolean
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
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
     * Set user
     *
     * @param Faucon\Bundle\ClubBundle\Entity\User $user
     */
    public function setUser(\Faucon\Bundle\ClubBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Faucon\Bundle\ClubBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
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
}