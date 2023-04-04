<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace Concrete\Package\SimpleNewsletter\Controller\Dialog\MailingLists;

use Bitter\SimpleNewsletter\Entity\Search\SavedMailingListSearch;
use Bitter\SimpleNewsletter\MailingList\Search\SearchProvider;
use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Entity\Search\SavedUserSearch;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

class AdvancedSearch extends AdvancedSearchController
{
    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }

    public function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedMailingListSearch::class)->findAll();
        }
    }

    public function getSubmitMethod()
    {
        return 'get';
    }

    public function getSubmitAction()
    {
        return $this->app->make('url')->to('/dashboard/simple_newsletter/mailing_lists', 'advanced_search');
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('mailing_list');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return $this->app->make('url')->to('/dashboard/simple_newsletter/mailing_lists', 'preset', $search->getID());
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/mailing_lists/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/mailing_lists/advanced_search/preset/edit?presetID=' . $search->getID());
    }

    public function getCurrentSearchBaseURL()
    {
        return (string)Url::to('/ccm/system/search/mailing_lists/current');
    }

    public function getBasicSearchBaseURL()
    {
        return (string)Url::to('/ccm/system/search/users/basic');
    }
}