<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Url;

?>

<div class="btn-group">
    <a href="<?php echo (string)Url::to("/dashboard/simple_newsletter/campaigns/add"); ?>" class="btn btn-success">
        <i class="fa fa-plus"></i> <?php echo t("Add"); ?>
    </a>
</div>