<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Service;

use Bitter\SimpleNewsletter\Config;
use Bitter\SimpleNewsletter\Entity\MailingList as MailingListEntity;
use Bitter\SimpleNewsletter\Entity\Subscriber as SubscriberEntity;
use Bitter\SimpleNewsletter\Enumeration\SubscriptionMethod;
use Bitter\SimpleNewsletter\Events\NewsletterConfirmation;
use Bitter\SimpleNewsletter\Events\NewsletterSubscribe;
use Bitter\SimpleNewsletter\Events\NewsletterUnsubscribe;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Mail\Service;
use Concrete\Core\Url\UrlImmutable;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Validator\String\EmailValidator;
use Doctrine\ORM\EntityManagerInterface;
use League\Url\UrlInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Exception;
use DateTime;

class Subscribe implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $entityManager;
    protected $eventDispatcher;
    protected $emailValidator;
    protected $logger;
    protected $config;
    protected $mailService;
    protected $idHelper;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcher $eventDispatcher,
        EmailValidator $emailValidator,
        LoggerFactory $loggerFactory,
        Service $mailService,
        Identifier $idHelper,
        Config $config
    )
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->emailValidator = $emailValidator;
        $this->logger = $loggerFactory->createLogger(Channels::META_CHANNEL_ALL);
        $this->mailService = $mailService;
        $this->idHelper = $idHelper;
        $this->config = $config;
    }

    /**
     * @param string $confirmationHash
     * @return ErrorList
     */
    public function confirm(
        $confirmationHash
    )
    {
        $errorList = new ErrorList();

        $subscriberEntry = $this->entityManager->getRepository(SubscriberEntity::class)->findOneBy(["confirmationHash" => $confirmationHash]);

        if ($subscriberEntry instanceof SubscriberEntity) {
            $subscriberEntry->setConfirmationHash("");
            $subscriberEntry->setIsConfirmed(true);
            $subscriberEntry->setConfirmedAt(new DateTime());
            $this->entityManager->persist($subscriberEntry);
            $this->entityManager->flush();
            $event = new NewsletterConfirmation();
            $event->setSubscriber($subscriberEntry);
            $this->app->make('director')->dispatch('on_newsletter_subscription_confirm', $event);
            $this->logger->info(t("Newsletter subscriber with mail address %s was confirmed.", $subscriberEntry->getEmail()));
        } else {
            $errorList->add(t("No newsletter subscriber was found by the given URL. Maybe the link is invalidated or you already have confirmed your subscription."));
        }

        return $errorList;
    }

    /**
     * @param string $email
     * @param int|array $mailingListIds
     * @param UrlImmutable|null $confirmationLink
     * @return ErrorList
     */
    public function subscribe(
        $email,
        $mailingListIds,
        $confirmationLink = null
    )
    {
        $errorList = new ErrorList();

        /*
         * Create or select the mail subscriber entity
         */

        $subscriberEntry = $this->entityManager->getRepository(SubscriberEntity::class)->findOneBy(["email" => $email]);

        if (!$subscriberEntry instanceof SubscriberEntity) {
            if ($this->emailValidator->isValid($email)) {
                $subscriberEntry = new SubscriberEntity();
                $subscriberEntry->setEmail($email);
                $subscriberEntry->setSubscribedAt(new DateTime());
                $subscriberEntry->setIsConfirmed($this->config->getSubscriptionMethod() === SubscriptionMethod::SINGLE_OPT_IN);
                if ($subscriberEntry->isConfirmed()) {
                    $subscriberEntry->setConfirmedAt(new DateTime());
                }
                $this->entityManager->persist($subscriberEntry);
                $this->entityManager->flush();
                $this->logger->info(t("New newsletter subscriber added with mail address %s.", $email));
            } else {
                $errorList->add(t("You need to enter a valid mail address."));
            }
        }

        /*
         * Send confirmation mail if double opt in is selected
         */

        if (!$errorList->has() &&
            $this->config->getSubscriptionMethod() === SubscriptionMethod::DOUBLE_OPT_IN &&
            !$subscriberEntry->isConfirmed() &&
            ($confirmationLink instanceof UrlImmutable || $confirmationLink instanceof UrlInterface)) {

            $confirmationHash = $this->idHelper->getString(32);

            $subscriberEntry->setConfirmationHash($confirmationHash);
            $this->entityManager->persist($subscriberEntry);
            $this->entityManager->flush();

            $confirmationLink = $confirmationLink->setQuery([
                "confirmationHash" => $confirmationHash
            ]);

            $this->mailService->to($email);
            $this->mailService->addParameter("confirmationLink", (string)$confirmationLink);
            $this->mailService->addParameter("email", $email);
            $this->mailService->load("confirm_subscription", "simple_newsletter");

            try {
                $this->mailService->sendMail();
                $this->logger->info(t("Sent confirmation mail to %s.", $email));
            } catch (Exception $err) {
                $this->logger->error(t("Error while sending confirmation mail to %s.", $email));
                $errorList->add(t("Error while sending the confirmation mail."));
            }
        }

        /*
         * Add subscriber to the selected mailing list(s)
         */

        if (is_numeric($mailingListIds)) {
            $mailingListIds = [$mailingListIds];
        }

        foreach ($mailingListIds as $mailingListId) {
            $mailingListEntry = $this->entityManager->getRepository(MailingListEntity::class)->findOneBy(["id" => $mailingListId]);

            if ($mailingListEntry instanceof MailingListEntity) {
                if (!$errorList->has()) {
                    $mailingListEntry->addSubscriber($subscriberEntry);
                    $this->entityManager->persist($mailingListEntry);
                    $this->entityManager->flush();
                    $event = new NewsletterSubscribe();
                    $event->setSubscriber($subscriberEntry);
                    $event->setMailingList($mailingListEntry);

                    $this->app->make('director')->dispatch('on_newsletter_subscribe', $event);

                    $this->logger->info(t("Newsletter subscriber with mail address %s added to mailing list %s.", $email, $mailingListEntry->getName()));
                }
            } else {
                $errorList->add(t("You need to select a valid mailing list."));
            }
        }

        return $errorList;
    }

    /**
     * @param string $email
     * @param array|int $mailingListIds
     * @return ErrorList
     */
    public function unsubscribe(
        $email,
        $mailingListIds
    )
    {
        $errorList = new ErrorList();

        $subscriberEntry = $this->entityManager->getRepository(SubscriberEntity::class)->findOneBy(["email" => $email]);

        if (!$subscriberEntry instanceof SubscriberEntity) {
            $errorList->add(t("The mail address is unknown."));
        } else {
            if (is_numeric($mailingListIds)) {
                $mailingListIds = [$mailingListIds];
            }

            foreach ($mailingListIds as $mailingListId) {
                $mailingListEntry = $this->entityManager->getRepository(MailingListEntity::class)->findOneBy(["id" => $mailingListId]);

                if ($mailingListEntry instanceof MailingListEntity) {
                    if ($mailingListEntry->getSubscribers()->contains($subscriberEntry)) {
                        $mailingListEntry->removeSubscriber($subscriberEntry);
                        $this->entityManager->persist($mailingListEntry);
                        $this->entityManager->flush();

                        $event = new NewsletterUnsubscribe();
                        $event->setSubscriber($subscriberEntry);
                        $event->setMailingList($mailingListEntry);
                        $this->app->make('director')->dispatch('on_newsletter_unsubscribe', $event);
                        $this->logger->info(t("Newsletter subscriber with mail address %s removed from mailing list %s.", $email, $mailingListEntry->getName()));

                    } else {
                        $errorList->add(t("The mail address is not associated with the mailing list %s.", $mailingListEntry->getName()));
                    }
                } else {
                    $errorList->add(t("You need to select a valid mailing list."));
                }
            }

            $db = $this->entityManager->getConnection();

            $totalSubscribedMailingLists = (int)$db->fetchColumn("SELECT COUNT(*) FROM SimpleNewsletterMailingListSubscribers WHERE subscriberId = ?", [$subscriberEntry->getId()]);

            if ($totalSubscribedMailingLists === 0) {
                $db->executeQuery("SET FOREIGN_KEY_CHECKS=0;");
                $db->executeQuery("DELETE FROM SimpleNewsletterSubscriber WHERE id = ?", [$subscriberEntry->getId()]);
                $db->executeQuery("SET FOREIGN_KEY_CHECKS=1;");
            }
        }

        return $errorList;
    }

}