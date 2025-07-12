<?php /** @noinspection PhpDeprecationInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Campaign;

use Bitter\SimpleNewsletter\Search\ItemList\Pager\Manager\CampaignListPagerManager;
use Concrete\Core\Search\ItemList\Database\ItemList;
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

class CampaignList extends ItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $isFulltextSearch = false;
    protected $autoSortColumns = ['c.name', 'c.subject', 'c.createdAt', 'c.sentAt'];
    protected $permissionsChecker = -1;

    public function createQuery()
    {
        $this->query->select('c.*')
            ->from("SimpleNewsletterCampaign", "c");
    }

    public function finalizeQuery(QueryBuilder $query): QueryBuilder
    {
        return $query;
    }

    public function filterByName($name, $exact = false)
    {
        if (strlen($name) > 0) {
            $this->isFulltextSearch = true;

            if ($exact) {
                $this->query->andWhere('c.name = :name');
                $this->query->setParameter('name', $name);
            } else {
                $this->query->andWhere(
                    $this->query->expr()->like('c.name', ':name')
                );
                $this->query->setParameter('name', '%' . $name . '%');
            }
        }
    }

    /**
     * @param int $mailingListId
     * @return void
     */
    public function filterByMailingList($mailingListId)
    {
        // @todo: to implement
    }

    /**
     * @param int $state
     * @return void
     */
    public function filterByState($state)
    {
        $this->query->andWhere('c.state = :state');
        $this->query->setParameter('state', $state);
    }

    /**
     *
     * @param \DateTime $date
     * @param mixed $comparison
     */
    public function filterByCreatedAt($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison(
            'c.createdAt',
            $comparison,
            $this->query->createNamedParameter($date)
        ));
    }

    /**
     *
     * @param \DateTime $date
     * @param mixed $comparison
     */
    public function filterBySentAt($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison(
            'c.sent',
            $comparison,
            $this->query->createNamedParameter($date)
        ));
    }

    public function filterBySubject($subject, $exact = false)
    {
        if (strlen($subject) > 0) {
            $this->isFulltextSearch = true;

            if ($exact) {
                $this->query->andWhere('c.subject = :subject');
                $this->query->setParameter('subject', $subject);
            } else {
                $this->query->andWhere(
                    $this->query->expr()->like('c.subject', ':subject')
                );
                $this->query->setParameter('subject', '%' . $subject . '%');
            }
        }
    }

    public function filterByKeywords($keywords, $exact = false)
    {
        $this->filterByName($keywords, $exact);
    }

    /**
     * @param array $mixed
     * @return \Bitter\SimpleNewsletter\Entity\Campaign
     * @throws BindingResolutionException
     */
    public function getResult($mixed): \Bitter\SimpleNewsletter\Entity\Campaign
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(\Bitter\SimpleNewsletter\Entity\Campaign::class)->findOneBy(["id" => $mixed["id"]]);
    }

    public function getTotalResults()
    {
        if ($this->permissionsChecker === -1) {
            return $this->deliverQueryObject()
                ->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct c.id)')
                ->setMaxResults(1)
                ->execute()
                ->fetchColumn();
        }

        return -1; // unknown
    }

    public function getPagerManager(): CampaignListPagerManager
    {
        return new CampaignListPagerManager($this);
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
                ->select('count(distinct c.id)')
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
}