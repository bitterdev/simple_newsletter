<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Entity;

use Bitter\SimpleNewsletter\Enumeration\CampaignState;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="SimpleNewsletterCampaign")
 */
class Campaign
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="`name`", type="string")
     */
    protected $name;

    /**
     * @var DateTime
     * @ORM\Column(name="`createdAt`", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(name="`sentAt`", type="datetime", nullable=true)
     */
    protected $sentAt;

    /**
     * @var string
     * @ORM\Column(name="`body`", type="text", nullable=true)
     */
    protected $body;

    /**
     * @var string
     * @ORM\Column(name="`subject`", type="string", nullable=true)
     */
    protected $subject;

    /**
     * @var int
     * @ORM\Column(name="`state`", type="string")
     */
    protected $state = CampaignState::DRAFT;

    /**
     * @var MailingList
     * @ORM\ManyToOne(targetEntity="Bitter\SimpleNewsletter\Entity\MailingList")
     * @ORM\JoinColumn(name="mailingListId", referencedColumnName="id")
     */
    protected $mailingList;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Campaign
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Campaign
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return Campaign
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * @param DateTime $sentAt
     * @return Campaign
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Campaign
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return Campaign
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     * @return Campaign
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return MailingList
     */
    public function getMailingList()
    {
        return $this->mailingList;
    }

    /**
     * @param MailingList $mailingList
     * @return Campaign
     */
    public function setMailingList($mailingList)
    {
        $this->mailingList = $mailingList;
        return $this;
    }

}
