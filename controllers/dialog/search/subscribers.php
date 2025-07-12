<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace Concrete\Package\SimpleNewsletter\Controller\Search;

use Bitter\SimpleNewsletter\Entity\Search\SavedSubscriberSearch;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Controller\Search\Standard;
use Concrete\Package\SimpleNewsletter\Controller\Dialog\Subscribers\AdvancedSearch;

class Subscribers extends Standard
{
    /**
     * @return \Concrete\Controller\Dialog\Search\AdvancedSearch
     */
    protected function getAdvancedSearchDialogController()
    {
        return $this->app->make(AdvancedSearch::class);
    }

    /**
     * @param int $presetID
     *
     * @return SavedSubscriberSearch|null
     */
    protected function getSavedSearchPreset($presetID)
    {
        $em = $this->app->make(EntityManagerInterface::class);
        return $em->find(SavedSubscriberSearch::class, $presetID);
    }

    /**
     * @return KeywordsField[]
     */
    protected function getBasicSearchFieldsFromRequest()
    {
        $fields = parent::getBasicSearchFieldsFromRequest();
        $keywords = htmlentities($this->request->get('cKeywords'), ENT_QUOTES, APP_CHARSET);
        if ($keywords) {
            $fields[] = new KeywordsField($keywords);
        }

        return $fields;
    }

    /**
     * @return bool
     */
    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}