<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\MailingList\Search\ColumnSet;

use Bitter\SimpleNewsletter\MailingList\Search\ColumnSet\Column\NameColumn;

class DefaultSet extends ColumnSet
{
    public function __construct()
    {
        $this->addColumn(new NameColumn());
        $date = $this->getColumnByKey('m.name');
        $this->setDefaultSortColumn($date, 'asc');
    }

}
