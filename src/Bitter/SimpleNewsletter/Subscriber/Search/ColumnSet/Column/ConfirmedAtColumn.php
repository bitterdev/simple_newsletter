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

class ConfirmedAtColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    public function getColumnKey(): string
    {
        return 's.confirmedAt';
    }

    public function getColumnName(): string
    {
        return t('Confirmed At');
    }

    public function getColumnCallback(): array
    {
        return ['\Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Available', 'getConfirmedAt'];
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
        $where = sprintf('s.confirmedAt %s :confirmedAt', $sort);
        $query->setParameter('confirmedAt', $mixed->getConfirmedAt());
        $query->andWhere($where);
    }

}
