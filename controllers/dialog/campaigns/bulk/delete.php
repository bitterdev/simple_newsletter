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

namespace Concrete\Package\SimpleNewsletter\Controller\Dialog\Campaigns\Bulk;

use Bitter\SimpleNewsletter\Entity\Campaign;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/campaigns/bulk/delete';
    protected $campaigns = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateCampaigns();

        $this->set('campaigns', $this->campaigns);
        $this->set('excluded', $this->excluded);
    }

    private function populateCampaigns()
    {
        $campaignIds = $this->request("item");

        if (is_array($campaignIds) && count($campaignIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($campaignIds as $campaignId) {
                $this->campaigns[] = $entityManager->getRepository(Campaign::class)->findOneBy(["id" => (int)$campaignId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateCampaigns();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->campaigns) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->campaigns as $campaign) {
                $entityManager->remove($campaign);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s campaign deleted', '%s campaigns deleted', $count));
        $r->setTitle(t('Campaigns Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/simple_newsletter/campaigns'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}
