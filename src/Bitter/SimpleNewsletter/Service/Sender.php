<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Service;

use Bitter\SimpleNewsletter\Entity\Campaign as CampaignEntity;
use Bitter\SimpleNewsletter\Entity\MailingList as MailingListEntity;
use Bitter\SimpleNewsletter\Entity\SendQueue;
use Bitter\SimpleNewsletter\Enumeration\CampaignState;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Mail\Service;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use Exception;

class Sender
{
    protected $entityManager;
    protected $logger;
    protected $mailService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerFactory $loggerFactory,
        Service $mailService
    )
    {
        $this->entityManager = $entityManager;
        $this->logger = $loggerFactory->createLogger(Channels::META_CHANNEL_ALL);
        $this->mailService = $mailService;
    }

    public function start()
    {
        $errorList = new ErrorList();

        /** @var CampaignEntity[] $queuedCampaigns */
        $queuedCampaigns = $this->entityManager->getRepository(CampaignEntity::class)->findBy(["state" => CampaignState::QUEUED]);

        if (is_array($queuedCampaigns) && !empty($queuedCampaigns)) {
            $hasSubscribers = false;

            foreach ($queuedCampaigns as $queuedCampaign) {
                $i = 0;

                $mailingList = $queuedCampaign->getMailingList();

                if ($mailingList instanceof MailingListEntity) {
                    foreach ($mailingList->getSubscribers() as $subscriber) {
                        if ($subscriber->isConfirmed()) {
                            $i++;
                            $queuedItem = new SendQueue();
                            $queuedItem->setSubscriber($subscriber);
                            $queuedItem->setCampaign($queuedCampaign);
                            $this->entityManager->persist($queuedItem);
                            $hasSubscribers = true;
                        }
                    }
                }

                if ($i > 0) {
                    $this->entityManager->flush();
                    $this->logger->info(t("Added campaign %s to send queue with %s subscribers.", $queuedCampaign->getName(), $i));
                }
            }

            if (!$hasSubscribers) {
                $errorList->add(t("There are no subscribers available in the campaigns associated mailing lists."));
            }
        } else {
            $errorList->add(t("There are no campaigns in the send queue."));
        }

        return $errorList;
    }

    public function getQueueItems()
    {
        $queueItems = [];

        /** @var SendQueue[] $sendQueueItems */
        $sendQueueItems = $this->entityManager->getRepository(SendQueue::class)->findAll();

        foreach ($sendQueueItems as $sendQueueItem) {
            $queueItems[] = $sendQueueItem->getId();
        }

        return $queueItems;
    }

    public function sendMail($queueId = null)
    {
        $errorList = new ErrorList();

        /** @var SendQueue $sendQueueItem */
        $sendQueueItem = $this->entityManager->getRepository(SendQueue::class)->findOneBy(["id" => $queueId]);

        if ($sendQueueItem instanceof SendQueue) {

            $this->mailService->addParameter("campaign", $sendQueueItem->getCampaign());;
            $this->mailService->addParameter("subscriber", $sendQueueItem->getSubscriber());
            $this->mailService->load("campaign", "simple_newsletter");
            $this->mailService->to($sendQueueItem->getSubscriber()->getEmail());

            try {
                $this->mailService->sendMail();
                $this->logger->info(t("Campaign %s was sent to subscriber with mail address %s.", $sendQueueItem->getCampaign()->getName(), $sendQueueItem->getSubscriber()->getEmail()));
            } catch (Exception $err) {
                $this->logger->error(t("Error while sending campaign %s to subscriber with mail address %s.", $sendQueueItem->getCampaign()->getName(), $sendQueueItem->getSubscriber()->getEmail()));
            }

            $this->entityManager->remove($sendQueueItem);
            $this->entityManager->flush();

        } else {
            $errorList->add(t("Invalid queue item."));
        }

        return $errorList;
    }

    public function sendAll()
    {
        $errorList = $this->start();

        if ($errorList->has()) {
            return $errorList;
        }

        foreach ($this->getQueueItems() as $queueItemId) {
            $errorList = $this->sendMail($queueItemId);

            if ($errorList->has()) {
                return $errorList;
            }
        }

        $errorList = $this->finish();

        return $errorList;
    }

    public function finish()
    {
        $errorList = new ErrorList();

        /** @var CampaignEntity[] $queuedCampaigns */
        $queuedCampaigns = $this->entityManager->getRepository(CampaignEntity::class)->findBy(["state" => CampaignState::QUEUED]);

        if (is_array($queuedCampaigns) && !empty($queuedCampaigns)) {
            foreach ($queuedCampaigns as $queuedCampaign) {
                $queuedCampaign->setState(CampaignState::SENT);
                $queuedCampaign->setSentAt(new DateTime());
                $this->entityManager->persist($queuedCampaign);
                $this->logger->info(t("Campaign %s was sent successfully to all subscribers of the associated mailing list.", $queuedCampaign->getName()));
            }
            $this->entityManager->flush();
        } else {
            $errorList->add(t("There are no campaigns in the send queue."));
        }

        return $errorList;
    }
}