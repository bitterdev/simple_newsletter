<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Search\ItemList\Pager\Manager;

use Bitter\SimpleNewsletter\Entity\MailingList;
use Bitter\SimpleNewsletter\Mailing\Search\ColumnSet\Available;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\Manager\AbstractPagerManager;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Support\Facade\Application;

class MailingListListPagerManager extends AbstractPagerManager
{

    /**
     * @param MailingList $mailingList
     * @return int
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function getCursorStartValue($mailing): int
    {
        return $mailing->getId();
    }

    public function getCursorObject($cursor)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        /** @noinspection PhpUnhandledExceptionInspection */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(MailingList::class)->findOneBy(["id" => $cursor]);
    }

    public function getAvailableColumnSet(): Available
    {
        return new Available();
    }

    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        $itemList->getQueryObject()->addOrderBy('m.id', $direction);
    }
}