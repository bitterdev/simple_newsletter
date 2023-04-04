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

namespace Concrete\Package\SimpleNewsletter\Controller\Dialog\MailingLists\Preset;

use Bitter\SimpleNewsletter\Entity\Search\SavedMailingListSearch;
use Concrete\Core\Permission\Key\Key;
use Concrete\Controller\Dialog\Search\Preset\Edit as PresetEdit;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Support\Facade\Url;

class Edit extends PresetEdit
{
    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }

    public function on_before_render()
    {
        parent::on_before_render();

        // use core views (remove package handle)
        $viewObject = $this->getViewObject();
        $viewObject->setInnerContentFile(null);
        $viewObject->setPackageHandle(null);
        $viewObject->setupRender();
    }

    public function getSavedSearchEntity()
    {
        /** @var EntityManager $em */
        $em = $this->app->make(EntityManager::class);

        if (is_object($em)) {
            return $em->getRepository(SavedMailingListSearch::class);
        }

        return null;
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string) Url::to('/ccm/system/search/mailing_lists/preset', $search->getID());
    }
}