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
 * @ORM\Table(name="SimpleNewsletterSendQueue")
 */
class SendQueue
{

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Campaign
     * @ORM\ManyToOne(targetEntity="Bitter\SimpleNewsletter\Entity\Campaign")
     * @ORM\JoinColumn(name="campaignId", referencedColumnName="id")
     */
    protected $campaign;

    /**
     * @var Subscriber
     * @ORM\ManyToOne(targetEntity="Bitter\SimpleNewsletter\Entity\Subscriber")
     * @ORM\JoinColumn(name="subscriberId", referencedColumnName="id")
     */
    protected $subscriber;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return SendQueue
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     * @return SendQueue
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
        return $this;
    }

    /**
     * @return Subscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @param Subscriber $subscriber
     * @return SendQueue
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;
        return $this;
    }
}