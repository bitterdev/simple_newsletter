<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace Concrete\Package\SimpleNewsletter\Controller\Dialog\MailingLists\Bulk;

use Bitter\SimpleNewsletter\Entity\MailingList;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/mailing_lists/bulk/delete';
    protected $mailingLists = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateMailingLists();

        $this->set('mailingLists', $this->mailingLists);
        $this->set('excluded', $this->excluded);
    }

    private function populateMailingLists()
    {
        $mailingListIds = $this->request("item");

        if (is_array($mailingListIds) && count($mailingListIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($mailingListIds as $mailingListId) {
                $this->mailingLists[] = $entityManager->getRepository(MailingList::class)->findOneBy(["id" => (int)$mailingListId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateMailingLists();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->mailingLists) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->mailingLists as $mailingList) {
                $entityManager->remove($mailingList);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s mailing list deleted', '%s mailing lists deleted', $count));
        $r->setTitle(t('Mailing Lists Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/simple_newsletter/mailing_lists'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}
