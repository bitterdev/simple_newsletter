<?php /** @noinspection PhpUnusedAliasInspection */
/** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Entity;

use Bitter\SimpleNewsletter\Attribute\Category\NewsletterSubscriberCategory;
use Bitter\SimpleNewsletter\Entity\Attribute\Value\SubscriberValue;
use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="SimpleNewsletterSubscriber")
 */
class Subscriber implements ObjectInterface
{
    use ObjectTrait;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="`email`", type="string")
     */
    protected $email;

    /**
     * @var bool
     * @ORM\Column(name="`isConfirmed`", type="boolean")
     */
    protected $isConfirmed = false;

    /**
     * @var DateTime
     * @ORM\Column(name="`subscribedAt`", type="datetime", nullable=true)
     */
    protected $subscribedAt;

    /**
     * @var DateTime
     * @ORM\Column(name="`confirmedAt`", type="datetime", nullable=true)
     */
    protected $confirmedAt;

    /**
     * @var Collection|MailingList[]
     *
     * @ORM\ManyToMany(targetEntity="Bitter\SimpleNewsletter\Entity\MailingList", mappedBy="subscribers")
     */
    protected $mailingLists;

    /**
     * @var string
     * @ORM\Column(name="`confirmationHash`", type="string")
     */
    protected $confirmationHash = '';

    public function __construct()
    {
        $this->mailingLists = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Subscriber
     */
    public function setId(int $id): Subscriber
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Subscriber
     */
    public function setEmail(?string $email): Subscriber
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param MailingList $mailingList
     */
    public function addMailingList(MailingList $mailingList)
    {
        if ($this->mailingLists->contains($mailingList)) {
            return;
        }

        $this->mailingLists->add($mailingList);
    }

    /**
     * @param MailingList $mailingList
     */
    public function removeMailingList(MailingList $mailingList)
    {
        if (!$this->mailingLists->contains($mailingList)) {
            return;
        }

        $this->mailingLists->removeElement($mailingList);
    }

    /**
     * @return MailingList[]|Collection
     */
    public function getMailingLists()
    {
        return $this->mailingLists;
    }

    public function getMailingListsArray()
    {
        $arrMailingLists = [];

        foreach($this->getMailingLists() as $mailingList) {
            $arrMailingLists[] = $mailingList->getId();
        }

        return $arrMailingLists;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * @param bool $isConfirmed
     * @return Subscriber
     */
    public function setIsConfirmed(bool $isConfirmed): Subscriber
    {
        $this->isConfirmed = $isConfirmed;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationHash(): string
    {
        return $this->confirmationHash;
    }

    /**
     * @param string $confirmationHash
     * @return Subscriber
     */
    public function setConfirmationHash(string $confirmationHash): Subscriber
    {
        $this->confirmationHash = $confirmationHash;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getSubscribedAt(): ?DateTime
    {
        return $this->subscribedAt;
    }

    /**
     * @param DateTime $subscribedAt
     * @return Subscriber
     */
    public function setSubscribedAt(?DateTime $subscribedAt): Subscriber
    {
        $this->subscribedAt = $subscribedAt;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getConfirmedAt(): ?DateTime
    {
        return $this->confirmedAt;
    }

    /**
     * @param DateTime $confirmedAt
     * @return Subscriber
     */
    public function setConfirmedAt(?DateTime $confirmedAt): Subscriber
    {
        $this->confirmedAt = $confirmedAt;
        return $this;
    }

    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!($ak instanceof AttributeKeyInterface)) {
            $ak = $ak ? $this->getObjectAttributeCategory()->getAttributeKeyByHandle((string)$ak) : null;
        }

        if ($ak === null) {
            $result = null;
        } else {
            $result = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);

            if ($result === null && $createIfNotExists) {
                $result = new SubscriberValue();
                $result->setSubscriber($this);
                $result->setAttributeKey($ak);
            }
        }

        return $result;
    }

    public function getObjectAttributeCategory(): NewsletterSubscriberCategory
    {
        $app = Application::getFacadeApplication();
        /** @noinspection PhpUnhandledExceptionInspection */
        return $app->make(NewsletterSubscriberCategory::class);
    }
}
