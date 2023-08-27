<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Bitter\SimpleNewsletter\Attribute\Category\NewsletterSubscriberCategory;
use Bitter\SimpleNewsletter\Config;
use Bitter\SimpleNewsletter\Entity\Campaign;
use Bitter\SimpleNewsletter\Entity\Subscriber;
use Bitter\SimpleNewsletter\Subscriber\SubscriberInfoRepository;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;

/** @var Campaign $campaign */
/** @var Subscriber $subscriber */

$subject = $campaign->getSubject();

$bodyHTML = $campaign->getBody();

$app = Application::getFacadeApplication();
/** @var Config $config */
$config = $app->make(Config::class);
/** @var CategoryService $service */
$service = $app->make(CategoryService::class);
$categoryEntity = $service->getByHandle('newsletter_subscriber');
/** @var NewsletterSubscriberCategory $category */
$category = $categoryEntity->getController();
$setManager = $category->getSetManager();

/** @var SubscriberInfoRepository $subscriberInfoRepository */
$subscriberInfoRepository = $app->make(SubscriberInfoRepository::class);
$subscriberInfo = $subscriberInfoRepository->getBySubscriberEntity($subscriber);

foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
    $placeholder = $ak->getAttributeKeyHandle();
    $value = (string)$subscriberInfo->getAttributeValue($ak);
    $bodyHTML = str_replace("[[" . $placeholder . "]]", $value, $bodyHTML);
}

$body = strip_tags(str_replace(["<br>", "<br/>"], "\r\n", $bodyHTML));

$body .=
    "\r\n" .
    t("Copy the following link to the address bar of your browser to unsubscribe from this mailing list:") . "\r\n" .
    (string)Url::to($config->getUnsubscribePage())->setQuery(["email" => $subscriber->getEmail()]);

ob_start();
/** @noinspection PhpUnhandledExceptionInspection */
View::element("newsletter_template", ["subject" => $subject, "bodyHTML" => $bodyHTML, "email" => $subscriber->getEmail(), "site" => $campaign->getMailingList()->getSite()], "simple_newsletter");
$bodyHTML = ob_get_contents();
ob_end_clean();