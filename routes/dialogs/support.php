<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Routing\Router;

/**
 * @var Router $router
 * Base path: /ccm/system/dialogs/simple_newsletter
 * Namespace: Concrete\Package\SimpleNewsletter\Controller\Dialog
 */

$router->all('/create_ticket', 'CreateTicket::view');
$router->all('/create_ticket/submit', 'CreateTicket::submit');