<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace Concrete\Package\SimpleNewsletter\Controller\Dialog\Subscribers\Bulk;

use Bitter\SimpleNewsletter\Entity\Subscriber;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/subscribers/bulk/delete';
    protected $subscribers = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateSubscribers();

        $this->set('subscribers', $this->subscribers);
        $this->set('excluded', $this->excluded);
    }

    private function populateSubscribers()
    {
        $subscriberIds = $this->request("item");

        if (is_array($subscriberIds) && count($subscriberIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($subscriberIds as $subscriberId) {
                $this->subscribers[] = $entityManager->getRepository(Subscriber::class)->findOneBy(["id" => (int)$subscriberId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateSubscribers();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->subscribers) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->subscribers as $subscriber) {

                foreach ($subscriber->getMailingLists() as $mailingList) {
                    $mailingList->removeSubscriber($subscriber);
                    $subscriber->removeMailingList($mailingList);
                    $entityManager->persist($mailingList);
                    $entityManager->flush();
                }

                $entityManager->remove($subscriber);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s subscriber deleted', '%s subscribers deleted', $count));
        $r->setTitle(t('Subscribers Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/simple_newsletter/subscribers'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}
