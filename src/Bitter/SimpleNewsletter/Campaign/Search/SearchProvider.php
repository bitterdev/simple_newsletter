<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Campaign\Search;

use Bitter\SimpleNewsletter\Campaign\CampaignList;
use Bitter\SimpleNewsletter\Entity\Search\SavedCampaignSearch;
use Bitter\SimpleNewsletter\Campaign\Campaign;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;
use Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\Available;
use Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\ColumnSet;
use Bitter\SimpleNewsletter\Campaign\Search\ColumnSet\DefaultSet;
use Bitter\SimpleNewsletter\Campaign\Search\Result\Result;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{
    public function getFieldManager()
    {
        return ManagerFactory::get('campaign');
    }

    public function __construct( Session $session)
    {
        parent::__construct($session);
    }

    public function getSessionNamespace(): string
    {
        return 'campaign';
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

    public function getItemList(): CampaignList
    {
        return new CampaignList();
    }

    public function getDefaultColumnSet(): DefaultSet
    {
        return new DefaultSet();
    }

    public function createSearchResultObject($columns, $list): Result
    {
        return new Result($columns, $list);
    }

    public function getSavedSearch(): SavedCampaignSearch
    {
        return new SavedCampaignSearch();
    }

    function getCustomAttributeKeys()
    {
        return [];
    }
}
