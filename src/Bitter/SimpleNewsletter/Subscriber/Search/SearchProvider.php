<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Subscriber\Search;

use Bitter\SimpleNewsletter\Attribute\Category\NewsletterSubscriberCategory;
use Bitter\SimpleNewsletter\Entity\Search\SavedSubscriberSearch;
use Bitter\SimpleNewsletter\Subscriber\SubscriberList;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;
use Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Available;
use Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\ColumnSet;
use Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\DefaultSet;
use Bitter\SimpleNewsletter\Subscriber\Search\Result\Result;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{

    protected $subscriberCategory;

    public function getFieldManager()
    {
        return ManagerFactory::get('subscriber');
    }

    public function __construct(NewsletterSubscriberCategory $subscriberCategory, Session $session)
    {
        $this->subscriberCategory = $subscriberCategory;

        parent::__construct($session);
    }

    public function getSessionNamespace(): string
    {
        return 'subscriber';
    }

    public function getCustomAttributeKeys(): array
    {
        return $this->subscriberCategory->getSearchableList();
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

    public function getItemList(): SubscriberList
    {
        return new SubscriberList();
    }

    public function getDefaultColumnSet(): DefaultSet
    {
        return new DefaultSet();
    }

    public function createSearchResultObject($columns, $list): Result
    {
        return new Result($columns, $list);
    }

    public function getSavedSearch(): SavedSubscriberSearch
    {
        return new SavedSubscriberSearch();
    }
}
