<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Service;

use Bitter\SimpleNewsletter\Entity\Campaign as CampaignEntity;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Doctrine\ORM\EntityManagerInterface;

class Campaign implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function getList()
    {
        $list = [];

        /** @var CampaignEntity[] $entries */
        $entries = $this->entityManager->getRepository(CampaignEntity::class)->findAll();

        foreach ($entries as $entry) {
            if ($entry instanceof CampaignEntity) {
                $list[$entry->getId()] = $entry->getName();
            }
        }

        return $list;
    }

}