<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router
            ->buildGroup()
            ->routes('api.php', 'simple_newsletter');

        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\SimpleNewsletter\Controller\Dialog\Support')
            ->setPrefix('/ccm/system/dialogs/simple_newsletter')
            ->routes('dialogs/support.php', 'simple_newsletter');

        /*
         * Subscribers
         */

        $router->buildGroup()->setNamespace('Concrete\Package\SimpleNewsletter\Controller\Dialog\Subscribers')
            ->setPrefix('/ccm/system/dialogs/subscribers')
            ->routes('dialogs/subscribers.php', 'simple_newsletter');

        $router->buildGroup()->setNamespace('Concrete\Package\SimpleNewsletter\Controller\Search')
            ->setPrefix('/ccm/system/search/subscribers')
            ->routes('search/subscribers.php', 'simple_newsletter');

        /*
         * Mailing Lists
         */

        $router->buildGroup()->setNamespace('Concrete\Package\SimpleNewsletter\Controller\Dialog\MailingLists')
            ->setPrefix('/ccm/system/dialogs/mailing_lists')
            ->routes('dialogs/mailing_lists.php', 'simple_newsletter');

        $router->buildGroup()->setNamespace('Concrete\Package\SimpleNewsletter\Controller\Search')
            ->setPrefix('/ccm/system/search/mailing_lists')
            ->routes('search/mailing_lists.php', 'simple_newsletter');

        /*
         * Campaigns
         */

        $router->buildGroup()->setNamespace('Concrete\Package\SimpleNewsletter\Controller\Dialog\Campaigns')
            ->setPrefix('/ccm/system/dialogs/campaigns')
            ->routes('dialogs/campaigns.php', 'simple_newsletter');

        $router->buildGroup()->setNamespace('Concrete\Package\SimpleNewsletter\Controller\Search')
            ->setPrefix('/ccm/system/search/campaigns')
            ->routes('search/campaigns.php', 'simple_newsletter');
    }
}