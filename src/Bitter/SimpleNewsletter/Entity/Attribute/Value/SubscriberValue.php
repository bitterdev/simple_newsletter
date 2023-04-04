<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Entity\Attribute\Value;

use Bitter\SimpleNewsletter\Entity\Subscriber;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="SubscriberAttributeValues"
 * )
 */
class SubscriberValue extends AbstractValue
{
    /**
     * @var Subscriber
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Bitter\SimpleNewsletter\Entity\Subscriber", inversedBy="attributes")
     * @ORM\JoinColumn(name="subscriberId", referencedColumnName="id")
     */
    protected $subscriber;

    /**
     * @return Subscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @param Subscriber $subscriber
     * @return SubscriberValue
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;
        return $this;
    }

}
