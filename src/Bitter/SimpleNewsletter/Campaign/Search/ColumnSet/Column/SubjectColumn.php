<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\Column;

use Bitter\SimpleNewsletter\Entity\Campaign;
use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class SubjectColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    public function getColumnKey(): string
    {
        return 'c.subject';
    }

    public function getColumnName(): string
    {
        return t('Subject');
    }

    public function getColumnCallback(): array
    {
        return ['\Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\Available', 'getSubject'];
    }

    /**
     * @param Campaign $itemList
     * @param $mixed Campaign
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('c.subject %s :subject', $sort);
        $query->setParameter('subject', $mixed->getSubject());
        $query->andWhere($where);
    }

}
