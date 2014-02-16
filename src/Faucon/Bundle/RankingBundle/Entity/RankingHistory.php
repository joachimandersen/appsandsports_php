<?php

namespace Faucon\Bundle\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Faucon\Bundle\RankingBundle\Entity\RankingHistory
 */
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="ranking_rankinghistory")
 * @ORM\Entity(repositoryClass="Faucon\Bundle\RankingBundle\Repository\RankingHistoryRepository")
 */
class RankingHistory
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
    private $ranking;
    
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Faucon\Bundle\ClubBundle\Entity\User", inversedBy="rankinghistorys")
     */
    private $owner;


    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="rankinghistorys")
     */
    private $category;

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
     * Set ranking
     *
     * @param integer $ranking
     */
    public function setRanking($ranking)
    {
        $this->ranking = $ranking;
    }

    /**
     * Get ranking
     *
     * @return integer 
     */
    public function getRanking()
    {
        return $this->ranking;
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
     * @ORM\prePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * Set owner
     *
     * @param Faucon\Bundle\ClubBundle\Entity\User $owner
     */
    public function setOwner(\Faucon\Bundle\ClubBundle\Entity\User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return Faucon\Bundle\ClubBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
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