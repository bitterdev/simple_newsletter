<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Events;

use Bitter\SimpleNewsletter\Entity\MailingList;
use Bitter\SimpleNewsletter\Entity\Subscriber;
use Symfony\Component\EventDispatcher\GenericEvent;

class NewsletterSubscribe extends GenericEvent
{
    /** @var Subscriber */
    protected $subscriber;
    /** @var MailingList */
    protected $mailingList;

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

    /**
     * @return MailingList
     */
    public function getMailingList()
    {
        return $this->mailingList;
    }

    /**
     * @param MailingList $mailingList
     * @return NewsletterSubscribe
     */
    public function setMailingList($mailingList)
    {
        $this->mailingList = $mailingList;
        return $this;
    }

}
