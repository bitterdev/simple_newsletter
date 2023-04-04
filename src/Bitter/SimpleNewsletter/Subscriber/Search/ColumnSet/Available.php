<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet;

use Bitter\SimpleNewsletter\Attribute\Key\NewsletterSubscriberKey;
use Bitter\SimpleNewsletter\Entity\Subscriber;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Illuminate\Contracts\Container\BindingResolutionException;
use Punic\Exception;
use Punic\Exception\BadArgumentType;

class Available extends DefaultSet
{
    protected $attributeClass = NewsletterSubscriberKey::class;

    /**
     * @param Subscriber $entry
     * @return string
     */
    public function getConfirmedAt(Subscriber $entry): string
    {
        $app = Application::getFacadeApplication();

        try {
            /** @var Date $dateHelper */
            $dateHelper = $app->make(Date::class);
            return $dateHelper->formatDateTime($entry->getConfirmedAt());
        } catch (BindingResolutionException|BadArgumentType|Exception $e) {
            return t("None");
        }
    }

    /**
     * @param Subscriber $entry
     * @return string
     */
    public function getConfirmed(Subscriber $entry): string
    {
        if ($entry->isConfirmed()) {
            return t("Yes");
        } else {
            return t("No");
        }
    }

    /**
     * @param Subscriber $entry
     * @return string
     */
    public function getEmail(Subscriber $entry): string
    {
        return $entry->getEmail();
    }

    /**
     * @param Subscriber $entry
     * @return string
     */
    public function getSubscribedAt(Subscriber $entry): string
    {
        $app = Application::getFacadeApplication();

        try {
            /** @var Date $dateHelper */
            $dateHelper = $app->make(Date::class);
            return $dateHelper->formatDateTime($entry->getSubscribedAt());
        } catch (BindingResolutionException|BadArgumentType|Exception $e) {
            return t("None");
        }
    }

    /**
     * @param Subscriber $entry
     * @return string
     */
    public function getSubscribedMailingLists(Subscriber $entry): string
    {
        $mailingLists = [];

        foreach($entry->getMailingLists() as $mailingList) {
            $mailingLists[] = $mailingList->getName();
        }

        return implode(", ", $mailingLists);
    }

    public function __construct()
    {
        parent::__construct();
    }
}
