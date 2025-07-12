<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Events;

use Bitter\SimpleNewsletter\Entity\MailingList;
use Bitter\SimpleNewsletter\Entity\Subscriber;
use Symfony\Component\EventDispatcher\GenericEvent;

class NewsletterConfirmation extends GenericEvent
{
    /** @var Subscriber */
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
     * @return NewsletterSubscribe
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;
        return $this;
    }


}
