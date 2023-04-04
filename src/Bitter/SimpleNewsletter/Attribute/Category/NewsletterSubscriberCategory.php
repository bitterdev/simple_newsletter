<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Attribute\Category;

use Bitter\SimpleNewsletter\Entity\Attribute\Key\NewsletterSubscriberKey;
use Bitter\SimpleNewsletter\Entity\Attribute\Value\SubscriberValue;
use Bitter\SimpleNewsletter\Subscriber\SubscriberInfo;
use Concrete\Core\Attribute\Category\AbstractStandardCategory;
use Concrete\Core\Entity\Attribute\Key\Key;

class NewsletterSubscriberCategory extends AbstractStandardCategory
{
    public function createAttributeKey()
    {
        return new NewsletterSubscriberKey();
    }

    public function getIndexedSearchTable()
    {
        return 'NewsletterSubscriberSearchIndexAttributes';
    }

    /**
     * @param SubscriberInfo $mixed
     * @return int
     */
    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getSubscriberId();
    }

    public function getSearchIndexFieldDefinition()
    {
        return [
            'columns' => [
                [
                    'name' => 'subscriberId',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'notnull' => true]
                ],
            ],
            'primary' => ['subscriberId']
        ];
    }

    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository(NewsletterSubscriberKey::class);
    }

    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository(SubscriberValue::class);
    }

    public function getAttributeValues($subscriber)
    {
        return $this->getAttributeValueRepository()->findBy([
            'subscriber' => $subscriber
        ]);
    }

    public function getAttributeValue(Key $key, $subscriber)
    {
        return $this->getAttributeValueRepository()->findOneBy([
            'subscriber' => $subscriber,
            'attribute_key' => $key
        ]);
    }

}
