<?php /** @noinspection PhpUnhandledExceptionInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\SimpleNewsletter\Entity\Attribute\Key\NewsletterSubscriberKey;
use Bitter\SimpleNewsletter\Entity\Subscriber;
use Bitter\SimpleNewsletter\Service\MailingList;
use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Concrete\Core\Attribute\Form\Renderer;

/** @var NewsletterSubscriberKey[] $attributes */
/** @var array $mailingLists */
/** @var Renderer $renderer */
/** @var Subscriber $subscriber */

$app = Application::getFacadeApplication();
/** @var EditorInterface $editor */
$editor = $app->make(EditorInterface::class);
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
/** @var MailingList $mailingListService */
$mailingListService = $app->make(MailingList::class);
/** @var \Concrete\Core\Form\Service\Widget\DateTime $dateTime */
$dateTime = $app->make(\Concrete\Core\Form\Service\Widget\DateTime::class);
?>
    <div class="ccm-dashboard-header-buttons">
        <?php \Concrete\Core\View\View::element("dashboard/help", [], "simple_newsletter"); ?>
    </div>

    <?php \Concrete\Core\View\View::element("dashboard/did_you_know", [], "simple_newsletter"); ?>

    <form action="#" method="post">
        <?php echo $token->output("update_subscriber"); ?>

        <div class="form-group">
            <?php echo $form->label("email", t("E-Mail")); ?>
            <?php echo $form->email("email", $subscriber->getEmail()); ?>
        </div>

        <div class="form-group">
            <div class="form-check">
                <?php echo $form->checkbox('isConfirmed', 1, $subscriber->isConfirmed(), ["class" => "form-check-input"]); ?>
                <?php echo $form->label('isConfirmed', t('Confirmed'), ["class" => "form-check-label"]); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("subscribedMailingLists", t("Subscribed Mailing Lists")); ?>
            <?php echo $form->selectMultiple("subscribedMailingLists", $mailingListService->getList(), $subscriber->getMailingListsArray()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("subscribedAt", t("Subscribed At")); ?>
            <?php echo $dateTime->datetime("subscribedAt", $subscriber->getSubscribedAt()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("confirmedAt", t("Confirmed At")); ?>
            <?php echo $dateTime->datetime("confirmedAt", $subscriber->getConfirmedAt()); ?>
        </div>

        <?php if (!empty($attributes)) {
            foreach ($attributes as $ak) {
                $renderer->buildView($ak)->render();
            }
        } ?>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo Url::to("/dashboard/simple_newsletter/subscribers"); ?>"
                   class="btn float-start btn-secondary">
                    <?php echo t("Back") ?>
                </a>

                <button type="submit" class="btn btn-primary float-end">
                    <i class="fas fa-save"></i> <?php echo t("Save"); ?>
                </button>
            </div>
        </div>
    </form>
