<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Subscriber;

use Bitter\SimpleNewsletter\Attribute\Category\NewsletterSubscriberCategory;
use Bitter\SimpleNewsletter\Attribute\Key\NewsletterSubscriberKey;
use Bitter\SimpleNewsletter\Entity\Attribute\Value\SubscriberValue;
use Bitter\SimpleNewsletter\Entity\Subscriber;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Foundation\ConcreteObject;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberInfo extends ConcreteObject implements AttributeObjectInterface
{
    use ObjectTrait;

    protected $attributeCategory;
    protected $entityManager;
    /** @var Subscriber */
    protected $entity;

    public function __construct(
        EntityManagerInterface $entityManager,
        NewsletterSubscriberCategory $attributeCategory
    )
    {
        $this->entityManager = $entityManager;
        $this->attributeCategory = $attributeCategory;
    }

    /**
     * @return int
     */
    public function getSubscriberId(): int
    {
        return $this->getEntity()->getId();
    }

    /**
     * @return Subscriber
     */
    public function getEntity(): Subscriber
    {
        return $this->entity;
    }

    /**
     * @param Subscriber $entity
     * @return SubscriberInfo
     */
    public function setEntity(Subscriber $entity): SubscriberInfo
    {
        $this->entity = $entity;
        return $this;
    }

    public function getObjectAttributeCategory()
    {
        return $this->attributeCategory;
    }

    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = NewsletterSubscriberKey::getByHandle($ak);
        }

        if ($ak instanceof \Bitter\SimpleNewsletter\Entity\Attribute\Key\NewsletterSubscriberKey) {
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this->entity);
        } else {
            $value = null;
        }

        if ($value === null && $createIfNotExists) {
            $value = new SubscriberValue();
            $value->setSubscriber($this->entity);
            $value->setAttributeKey($ak);
        }

        return $value;
    }

    /**
     * @param \Bitter\SimpleNewsletter\Entity\Attribute\Key\NewsletterSubscriberKey[] $attributes
     */
    public function saveUserAttributesForm(array $attributes)
    {
        foreach ($attributes as $uak) {
            $controller = $uak->getController();
            $value = $controller->createAttributeValueFromRequest();
            $this->setAttribute($uak, $value);
        }
    }
}