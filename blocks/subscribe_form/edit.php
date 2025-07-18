<?php /** @noinspection DuplicatedCode */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;

/** @var string $buttonText */
/** @var bool $displayCaptcha */
/** @var bool $displayTermsOfUse */
/** @var array $mailingLists */
/** @var null|int $selectedMailingList */
/** @var bool $displayPrivacy */
/** @var BlockView $view */
/** @var Form $form */
$mailingLists = [0 => t("*** Please select")] + $mailingLists;

$selectedMailingList = $selectedMailingList ?? 0;

\Concrete\Core\View\View::element("dashboard/help_blocktypes", [], "simple_newsletter");

/** @noinspection PhpUnhandledExceptionInspection */
View::element("dashboard/did_you_know", [], "simple_newsletter");
?>

<div class="form-group">
    <?php echo $form->label("buttonText", t('Button Text')); ?>
    <?php echo $form->text("buttonText", $buttonText, ["max-length" => 255]); ?>
</div>


<div class="form-group">
    <?php echo $form->label("selectedMailingList", t('Selected Mailing List')); ?>
    <?php echo $form->select("selectedMailingList", $mailingLists, $selectedMailingList); ?>
</div>

<div class="form-group">
    <div class="form-check">
        <?php echo $form->checkbox('displayTermsOfUse', 1, $displayTermsOfUse, ["class" => "form-check-input"]); ?>
        <?php echo $form->label('displayTermsOfUse', t("Display terms of use checkbox"), ["class" => "form-check-label"]); ?>
    </div>
</div>

<div class="form-group">
    <div class="form-check">
        <?php echo $form->checkbox('displayPrivacy', 1, $displayPrivacy, ["class" => "form-check-input"]); ?>
        <?php echo $form->label('displayPrivacy', t("Display privacy checkbox"), ["class" => "form-check-label"]); ?>
    </div>
</div>

<div class="form-group">
    <div class="form-check">
        <?php echo $form->checkbox('displayCaptcha', 1, $displayCaptcha, ["class" => "form-check-input"]); ?>
        <?php echo $form->label('displayCaptcha', t("Display captcha"), ["class" => "form-check-label"]); ?>
    </div>
</div>

<div class="help-block">
    <?php /** @noinspection HtmlUnknownTarget */
    echo t(
        "If you want to create a link to the terms of use and/or privacy page in the block view you need to setup the pages in the %s.",
        sprintf(
            "<a href=\"%s\" target=\"_blank\">%s</a>",
            Url::to("/dashboard/simple_newsletter/settings"),
            t("main settings")
        )
    ); ?>
</div>
