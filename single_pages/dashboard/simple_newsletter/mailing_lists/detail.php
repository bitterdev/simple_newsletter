<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\SimpleNewsletter\Entity\MailingList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var array $sites */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
/** @var MailingList $mailingList */

?>
    <div class="ccm-dashboard-header-buttons">
        <?php \Concrete\Core\View\View::element("dashboard/help", [], "simple_newsletter"); ?>
    </div>

    <?php \Concrete\Core\View\View::element("dashboard/did_you_know", [], "simple_newsletter"); ?>

    <form action="#" method="post">
        <?php echo $token->output("update_mailing_list"); ?>

        <div class="form-group">
            <?php echo $form->label("name", t("Name")); ?>
            <?php echo $form->text("name", $mailingList->getName()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("siteId", t("Site")); ?>
            <?php echo $form->select("siteId", $sites, $mailingList->getSite() instanceof \Concrete\Core\Entity\Site\Site ? $mailingList->getSite()->getSiteID() : null); ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions ">
                <a href="<?php echo (string)Url::to("/dashboard/simple_newsletter/mailing_lists"); ?>" class="btn float-start btn-secondary">
                    <?php echo t("Back")?>
                </a>

                <button type="submit" class="btn btn-primary float-end">
                    <i class="fas fa-save"></i> <?php echo t("Save"); ?>
                </button>
            </div>
        </div>
    </form>
