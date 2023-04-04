<?php /** @noinspection DuplicatedCode */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\SimpleNewsletter\Config;
use Bitter\SimpleNewsletter\Error\ErrorList\Formatter\BootstrapFormatter;
use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Package\SimpleNewsletter\Block\SubscribeForm\Controller;

/** @var array $mailingLists */
/** @var string $buttonText */
/** @var bool $displayCaptcha */
/** @var bool $displayTermsOfUse */
/** @var bool $displayPrivacy */
/** @var Controller $controller */
/** @var ErrorList $error */
/** @var string|bool $success */
/** @var string $email */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var Form $form */
$form = $app->make(Form::class);
/** @var CaptchaInterface $captcha */
$captcha = $app->make(CaptchaInterface::class);
/** @var Config $config */
$config = $app->make(Config::class);

?>

<div class="simple-newsletter-unsubscribe-block">
    <div class="simple-newsletter-unsubscribe-success">
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="simple-newsletter-unsubscribe-errors">
        <?php if ($error->has()) {
            $formatter = new BootstrapFormatter($error);
            echo $formatter->render();
        } ?>
    </div>

    <div class="simple-newsletter-unsubscribe-form">
        <form action="<?php echo $controller->getActionURL("unsubscribe"); ?>" method="post">
            <?php echo $token->output("unsubscribe_newsletter"); ?>

            <div class="form-group">
                <?php if (count($mailingLists) === 0): ?>
                    <p class="text-muted">
                        <?php /** @noinspection HtmlUnknownTarget */
                        echo t(
                            "No mailing lists available. Please go to the %s and create one.",
                            sprintf(
                                "<a href=\"%s\" target=\"_blank\">%s</a>",
                                Url::to("/dashboard/simple_newsletter/mailing_lists"),
                                t("mailing lists page")
                            )
                        ); ?>
                    </p>
                <?php elseif (count($mailingLists) === 1): ?>
                    <?php foreach ($mailingLists as $mailingListId => $mailingListLabel): ?>
                        <?php echo $form->hidden("mailingList[" . $mailingListId . "]", 1); ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php echo $form->label("mailingList", t("Unsubscribe from")); ?>

                    <?php foreach ($mailingLists as $mailingListId => $mailingListLabel): ?>
                        <div class="form-check">
                            <?php echo $form->checkbox("mailingList[" . $mailingListId . "]", $mailingListId, true, ["class" => "form-check-input", "id" => "mailingList-" . $mailingListId]); ?>
                            <?php echo $form->label("mailingList-" . $mailingListId, $mailingListLabel, ["class" => "form-check-label"]); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <?php echo $form->label("email", t("E-Mail")); ?>
                <?php echo $form->email("email", $email); ?>
            </div>

            <?php if ($displayCaptcha): ?>
                <div class="simple-newsletter-captcha">
                    <?php echo $captcha->display(); ?>

                    <div class="simple-newsletter-captcha-input">
                        <?php echo $captcha->showInput(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="simple-newsletter-subscribe-button">
                <?php echo $form->submit("subscribeNewsletter", $buttonText); ?>
            </div>
        </form>
    </div>
</div>