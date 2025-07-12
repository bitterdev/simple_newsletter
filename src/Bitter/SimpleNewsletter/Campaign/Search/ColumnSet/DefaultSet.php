<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Campaign\Search\ColumnSet;

use Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\Column\CreatedAtColumn;
use Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\Column\MailingListColumn;
use Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\Column\NameColumn;
use Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\Column\SentAtColumn;
use Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\Column\StateColumn;
use Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\Column\SubjectColumn;

class DefaultSet extends ColumnSet
{
    public function __construct()
    {
        $this->addColumn(new NameColumn());
        $this->addColumn(new SubjectColumn());
        $this->addColumn(new MailingListColumn());
        $this->addColumn(new StateColumn());
        $this->addColumn(new CreatedAtColumn());
        $this->addColumn(new SentAtColumn());
        $date = $this->getColumnByKey('c.name');
        $this->setDefaultSortColumn($date, 'asc');
    }

}
