<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Url;

?>

<div class="btn-group">
    <a href="<?php echo (string)Url::to("/dashboard/simple_newsletter/subscribers/attributes"); ?>" class="btn btn-default">
        <?php echo t("Attributes"); ?>
    </a>
</div>