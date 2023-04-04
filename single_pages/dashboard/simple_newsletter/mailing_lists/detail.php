<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\SimpleNewsletter\Entity\MailingList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
/** @var MailingList $mailingList */

?>
    <form action="#" method="post">
        <?php echo $token->output("update_mailing_list"); ?>

        <div class="form-group">
            <?php echo $form->label("name", t("Name")); ?>
            <?php echo $form->text("name", $mailingList->getName()); ?>
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