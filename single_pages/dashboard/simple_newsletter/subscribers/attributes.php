<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Core\View\View;

?>

<div class="ccm-dashboard-header-buttons">
    <?php \Concrete\Core\View\View::element("dashboard/help", [], "simple_newsletter"); ?>
</div>

<?php \Concrete\Core\View\View::element("dashboard/did_you_know", [], "simple_newsletter"); ?>

<?php
/** @var KeyList $attributeView */
$attributeView->render();
