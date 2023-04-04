<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\MailingList\Search;

use Bitter\SimpleNewsletter\Entity\Search\SavedMailingListSearch;
use Bitter\SimpleNewsletter\MailingList\MailingList;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;
use Bitter\SimpleNewsletter\MailingList\Search\ColumnSet\Available;
use Bitter\SimpleNewsletter\MailingList\Search\ColumnSet\ColumnSet;
use Bitter\SimpleNewsletter\MailingList\Search\ColumnSet\DefaultSet;
use Bitter\SimpleNewsletter\MailingList\Search\Result\Result;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{
    public function getFieldManager()
    {
        return ManagerFactory::get('mailing_list');
    }

    public function __construct( Session $session)
    {
        parent::__construct($session);
    }

    public function getSessionNamespace(): string
    {
        return 'mailing_list';
    }

    public function getBaseColumnSet(): ColumnSet
    {
        return new ColumnSet();
    }

    public function getAvailableColumnSet(): Available
    {
        return new Available();
    }

    public function getCurrentColumnSet(): DefaultSet
    {
        return ColumnSet::getCurrent();
    }

    public function getItemList(): MailingList
    {
        return new MailingList();
    }

    public function getDefaultColumnSet(): DefaultSet
    {
        return new DefaultSet();
    }

    public function createSearchResultObject($columns, $list): Result
    {
        return new Result($columns, $list);
    }

    public function getSavedSearch(): SavedMailingListSearch
    {
        return new SavedMailingListSearch();
    }

    function getCustomAttributeKeys()
    {
        return [];
    }
}
