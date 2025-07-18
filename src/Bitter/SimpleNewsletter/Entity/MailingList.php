<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SimpleNewsletterMailingList")
 */
class MailingList
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
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID", onDelete="CASCADE")
     *
     * @var \Concrete\Core\Entity\Site\Site|null
     */
    protected $site = null;

    /**
     * @var Collection|Subscriber[]
     *
     * @ORM\ManyToMany(targetEntity="Bitter\SimpleNewsletter\Entity\Subscriber", inversedBy="mailingLists")
     * @ORM\JoinTable(
     *  name="SimpleNewsletterMailingListSubscribers",
     *  joinColumns={
     *      @ORM\JoinColumn(name="`mailingListId`", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="`subscriberId`", referencedColumnName="id")
     *  }
     * )
     */
    protected $subscribers;

    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        $this->subscribers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return MailingList
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
     * @return MailingList
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param Subscriber $subscriber
     */
    public function addSubscriber($subscriber)
    {
        if ($this->subscribers->contains($subscriber)) {
            return;
        }

        $this->subscribers->add($subscriber);
    }

    /**
     * @param Subscriber $subscriber
     */
    public function removeSubscriber($subscriber)
    {
        if (!$this->subscribers->contains($subscriber)) {
            return;
        }

        $this->subscribers->removeElement($subscriber);
    }

    /**
     * @return Subscriber[]|Collection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @return \Concrete\Core\Entity\Site\Site|null
     */
    public function getSite(): ?\Concrete\Core\Entity\Site\Site
    {
        return $this->site;
    }

    /**
     * @param \Concrete\Core\Entity\Site\Site|null $site
     * @return MailingList
     */
    public function setSite(?\Concrete\Core\Entity\Site\Site $site): MailingList
    {
        $this->site = $site;
        return $this;
    }


}
