<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/system/search/campaigns
 * Namespace: Concrete\Package\SimpleNewsletter\Controller\Search\
 */

$router->all('/basic', 'Campaigns::searchBasic');
$router->all('/current', 'Campaigns::searchCurrent');
$router->all('/preset/{presetID}', 'Campaigns::searchPreset');
$router->all('/clear', 'Campaigns::clearSearch');