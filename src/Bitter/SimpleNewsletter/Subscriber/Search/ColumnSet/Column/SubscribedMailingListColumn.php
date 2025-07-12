<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Column;

use Bitter\SimpleNewsletter\Entity\Subscriber;
use Bitter\SimpleNewsletter\Subscriber\SubscriberList;
use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class SubscribedMailingListColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    public $isSortable = false;

    public function getColumnKey(): string
    {
        return 's.subscribedMailingLists';
    }

    public function getColumnName(): string
    {
        return t('Subscribed Mailing Lists');
    }

    public function getColumnCallback(): array
    {
        return ['\Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Available', 'getSubscribedMailingLists'];
    }

    /**
     * @param SubscriberList $itemList
     * @param $mixed Subscriber
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        // Do Nothing
    }

}
