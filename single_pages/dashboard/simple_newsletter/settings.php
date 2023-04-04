<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var string $legalInformation */
/** @var int $privacyPageId */
/** @var int $termsOfUsePageId */
/** @var int $unsubscribePageId */
/** @var string $subscriptionMethod */
/** @var array $subscriptionMethods */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);
/** @var Token $token */
$token = $app->make(Token::class);
?>

    <form action="#" method="post">
        <?php echo $token->output("update_settings"); ?>

        <div class="form-group">
            <?php echo $form->label("legalInformation", t('Legal information')); ?>
            <?php echo $form->text("legalInformation", $legalInformation, ["placeholder" => t("Your companies name with address in one line...")]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("subscriptionMethod", t('Subscription method')); ?>
            <?php echo $form->select("subscriptionMethod", $subscriptionMethods, $subscriptionMethod); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("privacyPageId", t('Privacy page')); ?>
            <?php echo $pageSelector->selectPage("privacyPageId", $privacyPageId); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("termsOfUsePageId", t('Terms of use page')); ?>
            <?php echo $pageSelector->selectPage("termsOfUsePageId", $termsOfUsePageId); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("unsubscribePageId", t('Unsubscribe page'), ["title" => t("You need to embed the unsubscribe block type to a page of your choice and select the page here. This page is required to generate the opt-out-url in the newsletters footer."), "class" => "launch-tooltip"]); ?>
            <?php echo $pageSelector->selectPage("unsubscribePageId", $unsubscribePageId); ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions ">
                <button type="submit" class="btn btn-primary float-end">
                    <i class="fas fa-save"></i> <?php echo t("Save"); ?>
                </button>
            </div>
        </div>
    </form>