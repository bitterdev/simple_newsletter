<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\MailingList\Search\ColumnSet;

use Bitter\SimpleNewsletter\Entity\MailingList;

class Available extends DefaultSet
{
    /**
     * @param MailingList $entry
     * @return string
     */
    public function getName(MailingList $entry): string
    {
        return $entry->getName();
    }

    public function __construct()
    {
        parent::__construct();
    }
}
