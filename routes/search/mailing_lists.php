<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/system/search/mailing_lists
 * Namespace: Concrete\Package\SimpleNewsletter\Controller\Search\
 */

$router->all('/basic', 'MailingLists::searchBasic');
$router->all('/current', 'MailingLists::searchCurrent');
$router->all('/preset/{presetID}', 'MailingLists::searchPreset');
$router->all('/clear', 'MailingLists::clearSearch');