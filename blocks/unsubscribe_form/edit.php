<?php /** @noinspection DuplicatedCode */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;

/** @var string $buttonText */
/** @var bool $displayCaptcha */
/** @var bool $displayTermsOfUse */
/** @var bool $displayPrivacy */
/** @var BlockView $view */
/** @var Form $form */

\Concrete\Core\View\View::element("dashboard/help_blocktypes", [], "simple_newsletter");

/** @noinspection PhpUnhandledExceptionInspection */
\Concrete\Core\View\View::element("dashboard/did_you_know", [], "simple_newsletter");
?>

<div class="form-group">
    <?php echo $form->label("buttonText", t('Button Text')); ?>
    <?php echo $form->text("buttonText", $buttonText, ["max-length" => 255]); ?>
</div>

<div class="form-group">
    <div class="form-check">
        <?php echo $form->checkbox('displayCaptcha', 1, $displayCaptcha, ["class" => "form-check-input"]); ?>
        <?php echo $form->label('displayCaptcha', t("Display captcha"), ["class" => "form-check-label"]); ?>
    </div>
</div>
