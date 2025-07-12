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
 * Base path: /ccm/system/search/subscribers
 * Namespace: Concrete\Package\SimpleNewsletter\Controller\Search\
 */

$router->all('/basic', 'Subscribers::searchBasic');
$router->all('/current', 'Subscribers::searchCurrent');
$router->all('/preset/{presetID}', 'Subscribers::searchPreset');
$router->all('/clear', 'Subscribers::clearSearch');