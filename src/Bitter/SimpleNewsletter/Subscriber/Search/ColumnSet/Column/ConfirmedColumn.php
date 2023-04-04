<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Column;

use Bitter\SimpleNewsletter\Entity\Subscriber;
use Bitter\SimpleNewsletter\Subscriber\SubscriberList;
use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class ConfirmedColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    public function getColumnKey(): string
    {
        return 's.isConfirmed';
    }

    public function getColumnName(): string
    {
        return t('Confirmed');
    }

    public function getColumnCallback(): array
    {
        return ['\Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Available', 'getConfirmed'];
    }

    /**
     * @param SubscriberList $itemList
     * @param $mixed Subscriber
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('s.isConfirmed %s :isConfirmed', $sort);
        $query->setParameter('isConfirmed', $mixed->isConfirmed());
        $query->andWhere($where);
    }

}
