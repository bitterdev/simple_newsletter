<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Service;

use Bitter\SimpleNewsletter\Entity\MailingList as MailingListEntity;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Site\Service;
use Doctrine\ORM\EntityManagerInterface;

class MailingList implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return MailingListEntity|object|null
     */
    public function getMailingListById($id)
    {
        return $this->entityManager->getRepository(MailingListEntity::class)->findOneBy([
            "id" => $id
        ]);
    }

    public function getList()
    {
        $list = [];

        /** @var MailingListEntity[] $entries */
        $entries = $this->entityManager->getRepository(MailingListEntity::class)->findAll();

        foreach ($entries as $entry) {
            if ($entry instanceof MailingListEntity) {
                $list[$entry->getId()] = $entry->getName();
            }
        }

        return $list;
    }

    public function getListByCurrentSite()
    {
        $list = [];

        /** @var Service $siteService */
        $siteService = $this->app->make(Service::class);
        $site = $siteService->getSite();
        /** @var MailingListEntity[] $entries */
        $entries = $this->entityManager->getRepository(MailingListEntity::class)->findBy(["site" => $site]);

        foreach ($entries as $entry) {
            if ($entry instanceof MailingListEntity) {
                $list[$entry->getId()] = $entry->getName();
            }
        }

        return $list;
    }

}