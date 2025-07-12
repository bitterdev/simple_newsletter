<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Campaign\Search\ColumnSet;

use Bitter\SimpleNewsletter\Entity\Campaign;
use Bitter\SimpleNewsletter\Entity\MailingList;
use Bitter\SimpleNewsletter\Enumeration\CampaignState;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Illuminate\Contracts\Container\BindingResolutionException;
use Punic\Exception;
use Punic\Exception\BadArgumentType;

class Available extends DefaultSet
{
    /**
     * @param Campaign $entry
     * @return string
     */
    public function getName(Campaign $entry): string
    {
        return $entry->getName();
    }

    /**
     * @param Campaign $entry
     * @return string
     */
    public function getState(Campaign $entry): string
    {
        $states = [
            CampaignState::SENT => t("Sent"),
            CampaignState::DRAFT => t("Draft"),
            CampaignState::QUEUED => t("Queued")
        ];

        return $states[$entry->getState()];
    }

    /**
     * @param Campaign $entry
     * @return string
     */
    public function getMailingList(Campaign $entry): string
    {
        $mailingList = $entry->getMailingList();

        if ($mailingList instanceof  MailingList) {
            return $mailingList->getName();
        }

        return "";
    }

    /**
     * @param Campaign $entry
     * @return string
     */
    public function getCreatedAt(Campaign $entry): string
    {
        $app = Application::getFacadeApplication();

        try {
            /** @var Date $dateHelper */
            $dateHelper = $app->make(Date::class);
            return $dateHelper->formatDateTime($entry->getCreatedAt());
        } catch (BindingResolutionException|BadArgumentType|Exception $e) {
            return t("None");
        }
    }

    /**
     * @param Campaign $entry
     * @return string
     */
    public function getSentAt(Campaign $entry): string
    {
        $app = Application::getFacadeApplication();

        try {
            /** @var Date $dateHelper */
            $dateHelper = $app->make(Date::class);
            return $dateHelper->formatDateTime($entry->getSentAt());
        } catch (BindingResolutionException|BadArgumentType|Exception $e) {
            return t("None");
        }
    }

    /**
     * @param Campaign $entry
     * @return string
     */
    public function getSubject(Campaign $entry): string
    {
        return $entry->getSubject();
    }

    public function __construct()
    {
        parent::__construct();
    }
}
