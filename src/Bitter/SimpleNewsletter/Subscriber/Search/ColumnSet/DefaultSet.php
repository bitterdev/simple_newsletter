<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet;

use Bitter\SimpleNewsletter\Attribute\Key\NewsletterSubscriberKey;
use Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Column\SubscribedAtColumn;
use Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Column\SubscribedMailingListColumn;
use Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Column\ConfirmedAtColumn;
use Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Column\ConfirmedColumn;
use Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Column\EmailColumn;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = NewsletterSubscriberKey::class;

    public function __construct()
    {
        $this->addColumn(new EmailColumn());
        $this->addColumn(new SubscribedAtColumn());
        $this->addColumn(new SubscribedMailingListColumn());
        $this->addColumn(new ConfirmedColumn());
        $this->addColumn(new ConfirmedAtColumn());
        $date = $this->getColumnByKey('s.subscribedAt');
        $this->setDefaultSortColumn($date, 'asc');
    }

}
