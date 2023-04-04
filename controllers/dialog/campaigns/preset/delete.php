<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Concrete\Package\SimpleNewsletter\Controller\Dialog\Campaigns\Preset;

use Bitter\SimpleNewsletter\Entity\Search\SavedCampaignSearch;
use Concrete\Controller\Dialog\Search\Preset\Delete as PresetDelete;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

class Delete extends PresetDelete
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
            return $em->getRepository(SavedCampaignSearch::class);
        }

        return null;
    }
}