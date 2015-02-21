<?php

namespace Faucon\Bundle\ClubBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Faucon\Bundle\ClubBundle\Entity\InvitationToken
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sport_club_invitation_token")
 * @ORM\Entity(repositoryClass="Faucon\Bundle\ClubBundle\Repository\InvitationTokenRepository")
 */
class InvitationToken
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $token
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;    

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=400)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="Faucon\Bundle\ClubBundle\Entity\Club", inversedBy="invitationtoken")
     */
    protected $club;

    /**
     * @var datetime $created
     *
     * @ORM\Column(name="used", type="datetime", nullable=true)
     */
    private $used;

    /**
     * @var datetime $sent
     *
     * @ORM\Column(name="sent", type="datetime", nullable=true)
     */
    private $sent;

    /**
     * @var datetime $created
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
     * Set token
     *
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set used
     *
     * @param datetime $used
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    /**
     * Get used
     *
     * @return datetime 
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Set sent
     *
     * @param datetime $sent
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    }

    /**
     * Get sent
     *
     * @return datetime 
     */
    public function getSent()
    {
        return $this->sent;
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
     * @ORM\prePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }
}
