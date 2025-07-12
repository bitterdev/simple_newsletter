<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Navigation\Breadcrumb\Dashboard;

use Bitter\SimpleNewsletter\Entity\Subscriber;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardBreadcrumbFactory;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Page;

class DashboardSubscriberBreadcrumbFactory implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var DashboardBreadcrumbFactory
     */
    protected $breadcrumbFactory;

    /**
     * @var Navigation
     */
    protected $navigation;

    public function __construct(DashboardBreadcrumbFactory $breadcrumbFactory, Navigation $navigation)
    {
        $this->breadcrumbFactory = $breadcrumbFactory;
        $this->navigation = $navigation;
    }

    public function getBreadcrumb(Page $dashboardPage, $mixed = null): BreadcrumbInterface
    {
        $breadcrumb = $this->breadcrumbFactory->getBreadcrumb($dashboardPage);

        if ($mixed instanceof Subscriber) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $item = new Item(
                $this->app->make('url')->to(
                    '/dashboard/simple_newsletter/subscribers', 'update', $mixed->getId()
                ),
                $mixed->getEmail()
            );

            $breadcrumb->add($item);
        }

        return $breadcrumb;
    }
}
