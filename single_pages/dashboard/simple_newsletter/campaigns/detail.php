<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\SimpleNewsletter\Entity\Campaign;
use Bitter\SimpleNewsletter\Entity\MailingList;
use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;

/** @var array $mailingLists */
/** @var Campaign $campaign */

$app = Application::getFacadeApplication();
/** @var EditorInterface $editor */
$editor = $app->make(EditorInterface::class);
/** @var Form $form */
$form = $app->make(Form::class);
?>
    <div class="ccm-dashboard-header-buttons">
        <?php \Concrete\Core\View\View::element("dashboard/help", [], "simple_newsletter"); ?>
    </div>

    <?php \Concrete\Core\View\View::element("dashboard/did_you_know", [], "simple_newsletter"); ?>
    
    <form action="#" method="post">
        <?php echo $token->output("update_campaign"); ?>

        <div class="form-group">
            <?php echo $form->label("name", t('Name')); ?>
            <?php echo $form->text("name", $campaign->getName()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("mailingList", t('Mailing List')); ?>
            <?php echo $form->select("mailingList", $mailingLists, $campaign->getMailingList() instanceof MailingList ? $campaign->getMailingList()->getId() : null); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("body", t('Subject')); ?>
            <?php echo $form->text("subject", $campaign->getSubject()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("body", t('Body')); ?>
            <?php echo $editor->outputStandardEditor("body", $campaign->getBody()); ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo (string)Url::to("/dashboard/simple_newsletter/campaigns"); ?>"
                   class="btn float-start btn-secondary">
                    <?php echo t("Back") ?>
                </a>

                <button type="submit" class="btn btn-primary float-end">
                    <i class="fas fa-save"></i> <?php echo t("Save"); ?>
                </button>
            </div>
        </div>
    </form>
