<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Search\ItemList\Pager\Manager;

use Bitter\SimpleNewsletter\Entity\Subscriber;
use Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Available;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\Manager\AbstractPagerManager;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Support\Facade\Application;

class SubscriberListPagerManager extends AbstractPagerManager
{

    /**
     * @param Subscriber $subscriber
     * @return int
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function getCursorStartValue($subscriber): int
    {
        return $subscriber->getId();
    }

    public function getCursorObject($cursor)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        /** @noinspection PhpUnhandledExceptionInspection */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(Subscriber::class)->findOneBy(["id" => $cursor]);
    }

    public function getAvailableColumnSet(): Available
    {
        return new Available();
    }

    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        $itemList->getQueryObject()->addOrderBy('s.id', $direction);
    }
}