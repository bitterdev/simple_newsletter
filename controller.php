<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleNewsletter;

use Bitter\SimpleNewsletter\Console\Command\SendNewsletter;
use Bitter\SimpleNewsletter\Provider\ServiceProvider;
use Concrete\Core\Package\Package;
use Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface;
use Concrete\Core\Database\EntityManager\Provider\StandardPackageProvider;

class Controller extends Package implements ProviderAggregateInterface
{
    protected $pkgHandle = 'simple_newsletter';
    protected $pkgVersion = '1.5.2';
    protected $appVersionRequired = '8.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/SimpleNewsletter' => 'Bitter\SimpleNewsletter',
    ];

    public function getPackageDescription()
    {
        return t('Powerful standalone newsletter solution that fully complies with GDPR.');
    }

    public function getPackageName()
    {
        return t('Simple Newsletter');
    }

    public function getEntityManagerProvider()
    {
        return new StandardPackageProvider($this->app, $this, [
            'src/Bitter/SimpleNewsletter/Entity' => 'Bitter\SimpleNewsletter\Entity'
        ]);
    }

    public function on_start()
    {
        if ($this->app->isRunThroughCommandLineInterface()) {
            $console = $this->app->make('console');
            $console->add(new SendNewsletter());
        }

        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    public function install()
    {
        parent::install();
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
        $this->installContentFile('install.xml');
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile('install.xml');
    }

}