<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\MailingList\Search\ColumnSet\Column;

use Bitter\SimpleNewsletter\Entity\MailingList;
use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class NameColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    public function getColumnKey(): string
    {
        return 'm.name';
    }

    public function getColumnName(): string
    {
        return t('Name');
    }

    public function getColumnCallback(): array
    {
        return ['\Bitter\SimpleNewsletter\MailingList\Search\ColumnSet\Available', 'getName'];
    }

    /**
     * @param MailingList $itemList
     * @param $mixed MailingList
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('m.name %s :name', $sort);
        $query->setParameter('name', $mixed->getName());
        $query->andWhere($where);
    }

}
