<?php

namespace Faucon\Bundle\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Faucon\Bundle\RankingBundle\Entity\Ranking
 */
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="ranking_ranking")
 * @ORM\Entity(repositoryClass="Faucon\Bundle\RankingBundle\Repository\RankingRepository")
 */
class Ranking
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
     * @ORM\Column(type="datetime", length="255")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="rankings")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="\Faucon\Bundle\ClubBundle\Entity\User", inversedBy="rankings")
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="\Faucon\Bundle\ClubBundle\Entity\User", inversedBy="rankings")
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
     * Set player
     *
     * @param Faucon\Bundle\ClubBundle\Entity\User $player
     */
    public function setPlayer(\Faucon\Bundle\ClubBundle\Entity\User $player)
    {
        $this->player = $player;
    }

    /**
     * Get player
     *
     * @return Faucon\Bundle\ClubBundle\Entity\User 
     */
    public function getPlayer()
    {
        return $this->player;
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
        return $this->getPlayer().' ('.$this->getRanking().')';
    }

}