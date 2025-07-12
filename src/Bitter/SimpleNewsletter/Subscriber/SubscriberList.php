<?php /** @noinspection PhpDeprecationInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Subscriber;

use Bitter\SimpleNewsletter\Attribute\Key\NewsletterSubscriberKey;
use Bitter\SimpleNewsletter\Entity\Subscriber;
use Bitter\SimpleNewsletter\Search\ItemList\Pager\Manager\SubscriberListPagerManager;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Closure;
use DateTime;

class SubscriberList extends AttributedItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $isFulltextSearch = false;
    protected $autoSortColumns = ['s.email', 's.confirmedAt', 's.subscribedAt', 's.isConfirmed'];
    protected $permissionsChecker = -1;

    public function createQuery()
    {
        $this->query->select('s.*')
            ->from("SimpleNewsletterSubscriber", "s")
            ->leftJoin('s', 'NewsletterSubscriberSearchIndexAttributes', 'at', 's.id = at.subscriberId');
    }

    public function finalizeQuery(QueryBuilder $query): QueryBuilder
    {
        return $query;
    }

    /**
     * @param string $keywords
     */
    public function filterByKeywords(string $keywords)
    {
        $this->query->andWhere('(s.`email` LIKE :keywords)');
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }
    /**
     * @param bool $isConfirmed
     * @return void
     */
    public function filterByConfirmed(bool $isConfirmed)
    {
        $this->query->andWhere('s.isConfirmed = :confirmed');
        $this->query->setParameter('confirmed', $isConfirmed);
    }

    /**
     * @param int $mailingListId
     * @return void
     */
    public function filterByMailingList(int $mailingListId)
    {
        // @todo: to implement
    }

    public function filterByEmail($email, $exact = false)
    {
        if (strlen($email) > 0) {
            $this->isFulltextSearch = true;

            if ($exact) {
                $this->query->andWhere('s.email = :email');
                $this->query->setParameter('email', $email);
            } else {
                $this->query->andWhere(
                    $this->query->expr()->like('s.email', ':email')
                );
                $this->query->setParameter('email', '%' . $email . '%');
            }
        }
    }

    /**
     *
     * @param DateTime $date
     * @param mixed $comparison
     */
    public function filterByConfirmedAt($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison(
            's.confirmedAt',
            $comparison,
            $this->query->createNamedParameter($date)
        ));
    }

    /**
     *
     * @param DateTime $date
     * @param mixed $comparison
     */
    public function filterBySubscribedAt( $date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison(
            's.subscribedAt',
            $comparison,
            $this->query->createNamedParameter($date)
        ));
    }


    /**
     * @param array $mixed
     * @return Subscriber
     * @throws BindingResolutionException
     */
    public function getResult($mixed): Subscriber
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(Subscriber::class)->findOneBy(["id" => $mixed["id"]]);
    }

    public function getTotalResults()
    {
        if ($this->permissionsChecker === -1) {
            return $this->deliverQueryObject()
                ->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t2.id)')
                ->setMaxResults(1)
                ->execute()
                ->fetchColumn();
        }

        return -1; // unknown
    }

    public function getPagerManager(): SubscriberListPagerManager
    {
        return new SubscriberListPagerManager($this);
    }

    public function getPagerVariableFactory(): VariableFactory
    {
        return new VariableFactory($this, $this->getSearchRequest());
    }

    /** @noinspection PhpDeprecationInspection */
    public function getPaginationAdapter(): DoctrineDbalAdapter
    {
        /** @noinspection PhpDeprecationInspection */
        return new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct s.id)')
                ->setMaxResults(1);
        });
    }

    public function checkPermissions($mixed)
    {
        if (isset($this->permissionsChecker)) {
            if ($this->permissionsChecker === -1) {
                return true;
            }

            /** @noinspection PhpParamsInspection */
            return call_user_func_array($this->permissionsChecker, [$mixed]);
        }

        $user = new User();
        return $user->isSuperUser();
    }

    public function setPermissionsChecker(Closure $callback = null)
    {
        $this->permissionsChecker = $callback;
    }

    public function ignorePermissions()
    {
        $this->permissionsChecker = -1;
    }

    public function getPermissionsChecker(): int
    {
        return $this->permissionsChecker;
    }

    public function enablePermissions()
    {
        unset($this->permissionsChecker);
    }

    public function isFulltextSearch(): bool
    {
        return $this->isFulltextSearch;
    }

    protected function getAttributeKeyClassName(): string
    {
        return NewsletterSubscriberKey::class;
    }
}