<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Provider;

use Bitter\SimpleNewsletter\Attribute\Category\Manager;
use Bitter\SimpleNewsletter\Command\Task\Controller\SendNewsletterController;
use Bitter\SimpleNewsletter\RouteList;
use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Routing\Router;
use Concrete\Core\Editor\Plugin;
use Concrete\Core\Asset\AssetList;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->initializeRoutes();
        $this->overrideAttributeCategoryManager();
        $this->registerAssets();
        $this->registerEditorPlugins();
        $this->extendTaskControllers();
        $this->initializeSearchProviders();
    }

    private function initializeSearchProviders()
    {
        $this->app->singleton('manager/search_field/subscriber', function($app) {
            return $this->app->make(\Bitter\SimpleNewsletter\Subscriber\Search\Field\Manager::class);
        });

        $this->app->singleton('manager/search_field/mailing_list', function($app) {
            return $this->app->make(\Bitter\SimpleNewsletter\MailingList\Search\Field\Manager::class);
        });

        $this->app->singleton('manager/search_field/campaign', function($app) {
            return $this->app->make(\Bitter\SimpleNewsletter\Campaign\Search\Field\Manager::class);
        });
    }

    private function initializeRoutes()
    {
        /** @var Router $router */
        $router = $this->app->make("router");
        $list = new RouteList();
        $list->loadRoutes($router);
    }

    private function overrideAttributeCategoryManager()
    {
        $this->app->singleton('manager/attribute/category', function ($app) {
            return new Manager($app);
        });
    }

    private function extendTaskControllers()
    {
        $manager = $this->app->make(\Concrete\Core\Command\Task\Manager::class);
        $manager->extend('send_newsletter', static function () {
            return new SendNewsletterController();
        });
    }

    private function registerAssets()
    {
        $al = AssetList::getInstance();
        $al->register('javascript', 'editor/ckeditor4/newsletterplaceholders', 'js/ckeditor4/plugins/newsletterplaceholders/register.js', [], 'simple_newsletter');
        $al->registerGroup('editor/ckeditor4/newsletterplaceholders', [['javascript', 'editor/ckeditor4/newsletterplaceholders']]);
    }

    private function registerEditorPlugins()
    {
        /** @var EditorInterface $editor */
        $editor = $this->app->make(EditorInterface::class);
        $pluginManager = $editor->getPluginManager();

        $plugin = new Plugin();
        $plugin->setKey('newsletterplaceholders');
        $plugin->setName(t('Newsletter placeholders'));
        $plugin->requireAsset('editor/ckeditor4/newsletterplaceholders');

        if (!$pluginManager->isAvailable($plugin)) {
            $pluginManager->register($plugin);
        }
        if (!$pluginManager->isSelected($plugin)) {
            $key = $plugin->getKey();
            $pluginManager->select($key);
        }
    }
}