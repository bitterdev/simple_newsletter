<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\View\View;

/** @var string $confirmationLink */
/** @var string $email */

$subject = t('Confirm newsletter subscription');

$bodyHTML = t("Hello,") . "<br>";
$bodyHTML .= "<br>";
$bodyHTML .= t("thank you for your newsletter subscription.") . "<br>";
$bodyHTML .= "<br>";
$bodyHTML .= t("To complete your subscription request, please click the confirmation link below.") . "<br>";
$bodyHTML .= "<br>";
$bodyHTML .= t("If you can't click the confirmation link, please copy the link manually and paste it to the address bar of your browser.") . "<br>";
$bodyHTML .= "<br>";
$bodyHTML .= sprintf(
        "<a href=\"%s\" target=\"_blank\">%s</a>",
        $confirmationLink,
        $confirmationLink
    ) . "<br>";

$body = strip_tags(str_replace(["<br>", "<br/>"], "\r\n", $bodyHTML));

ob_start();
/** @noinspection PhpUnhandledExceptionInspection */
View::element("newsletter_template", ["subject" => $subject, "bodyHTML" => $bodyHTML, "email" => $email], "simple_newsletter");
$bodyHTML = ob_get_contents();
ob_end_clean();