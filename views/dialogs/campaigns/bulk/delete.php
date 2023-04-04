<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Bitter\SimpleNewsletter\Entity\Campaign;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Number;

/** @var $controller */
/** @var Campaign[] $campaigns */

$app = Application::getFacadeApplication();
/** @var Number $nh */
$nh = $app->make(Number::class);

?>

<?php if (!is_array($campaigns) || count($campaigns) == 0) { ?>
    <div class="alert-message info">
        <?php echo t("No campaigns are eligible for this operation"); ?>
    </div>
<?php } else { ?>
    <p>
        <?php echo t('Are you sure you would like to delete the following campaigns?'); ?>
    </p>

    <form method="post" data-dialog-form="bulk-remove-campaigns" action="<?php echo $controller->action('submit'); ?>">
        <?php foreach ($campaigns as $campaign) { ?>
            <input type="hidden" name="item[]" value="<?php echo $campaign->getId(); ?>"/>
        <?php } ?>

        <div class="ccm-ui">
            <table class="table table-striped" width="100%" cellspacing="0" cellpadding="0" border="0">
                <thead>
                <tr>
                    <th>
                        <?php echo t('Name') ?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($campaigns as $campaign) { ?>
                    <tr>
                        <td>
                            <?php echo $campaign->getName(); ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary" data-dialog-action="cancel">
                <?php echo t('Cancel'); ?>
            </button>

            <button type="button" data-dialog-action="submit" class="btn btn-primary ms-auto">
                <?php echo t('Delete'); ?>
            </button>
        </div>

    </form>
<?php } ?>
