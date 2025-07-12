<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Concrete\Package\SimpleNewsletter\Controller\SinglePage\Dashboard\SimpleNewsletter;

use Bitter\SimpleNewsletter\Entity\MailingList;
use Bitter\SimpleNewsletter\Entity\Search\SavedMailingListSearch;
use Bitter\SimpleNewsletter\Form\Service\Validation;
use Bitter\SimpleNewsletter\Navigation\Breadcrumb\Dashboard\DashboardMailingListBreadcrumbFactory;
use Bitter\SimpleNewsletter\MailingList\Search\Menu\MenuFactory;
use Bitter\SimpleNewsletter\MailingList\Search\SearchProvider;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Site\Service;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\SimpleNewsletter\Controller\Element\Dashboard\SimpleNewsletter\MailingLists\Header;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\HttpFoundation\Response;

class MailingLists extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var Validation */
    protected $validation;
    /** @var Element */
    protected $headerMenu;
    /** @var Element */
    protected $headerSearch;

    /**
     * @return SearchProvider
     * @throws BindingResolutionException
     */
    protected function getSearchProvider(): SearchProvider
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(SearchProvider::class);
    }

    /**
     * @return QueryFactory
     * @throws BindingResolutionException
     */
    protected function getQueryFactory(): QueryFactory
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(QueryFactory::class);
    }

    protected function getHeaderMenu(): Element
    {
        if (!isset($this->headerMenu)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->headerMenu = $this->app->make(ElementManager::class)->get('mailing_lists/search/menu', 'simple_newsletter');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch(): Element
    {
        if (!isset($this->headerSearch)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->headerSearch = $this->app->make(ElementManager::class)->get('mailing_lists/search/search', 'simple_newsletter');
        }

        return $this->headerSearch;
    }

    /**
     * @param Result $result
     * @throws BindingResolutionException
     */
    protected function renderSearchResult(Result $result)
    {
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $headerMenu->getElementController()->setQuery($result->getQuery());
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $headerSearch->getElementController()->setQuery($result->getQuery());

        $this->set('resultsBulkMenu', $this->app->make(MenuFactory::class)->createBulkMenu());
        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);

        $this->setThemeViewTemplate('full.php');
    }

    /**
     * @param Query $query
     * @return Result
     * @throws BindingResolutionException
     */
    protected function createSearchResult(Query $query): Result
    {
        $provider = $this->app->make(SearchProvider::class);
        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));

        $query = $queryModifier->process($query);

        return $resultFactory->createFromQuery($provider, $query);
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    protected function getSearchKeywordsField()
    {
        $keywords = null;

        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }

        return new KeywordsField($keywords);
    }

    /**
     * @throws BindingResolutionException
     */
    public function advanced_search()
    {
        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
            $this->getSearchProvider(), $this->request, Request::METHOD_GET
        );

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);
    }

    /**
     * @throws BindingResolutionException
     */
    public function preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedMailingListSearch::class, $presetID);

            if ($preset) {
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);

                return;
            }
        }

        $this->view();
    }

    /**
     * @return DashboardMailingListBreadcrumbFactory
     * @throws BindingResolutionException
     */
    protected function createBreadcrumbFactory(): DashboardMailingListBreadcrumbFactory
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(DashboardMailingListBreadcrumbFactory::class);
    }

    public function on_start()
    {
        parent::on_start();
        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->validation = $this->app->make(Validation::class);
    }

    private function validate()
    {
        $this->validation->setData($this->request->request->all());

        $this->validation->addRequiredToken("update_mailing_list");
        $this->validation->addRequired("name", t("You need to enter a valid name."));

        if (!$this->validation->test()) {
            $this->error = $this->validation->getError();
            return false;
        } else {
            return true;
        }
    }

    private function getSites(): array
    {
        $sites = [];

        /** @var Service $siteService */
        $siteService = $this->app->make(Service::class);

        foreach($siteService->getList() as $site) {
            $sites[$site->getSiteID()] = $site->getSiteName();
        }

        return $sites;
    }

    public function add()
    {
        if ($this->request->getMethod() === "POST" && $this->validate()) {
            $mailingList = new MailingList();
            $mailingList->setName($this->request->request->get("name"));
            /** @var Service $siteService */
            $siteService = $this->app->make(Service::class);
            $site = $siteService->getByID($this->request->request->get("siteId"));
            $mailingList->setSite($site);
            $this->entityManager->persist($mailingList);
            $this->entityManager->flush();
            return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/mailing_lists/added"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->set('sites', $this->getSites());
        $this->set('mailingList', new MailingList());

        $this->render("/dashboard/simple_newsletter/mailing_lists/detail", "simple_newsletter");
    }

    public function added()
    {
        $this->set('success', t('The mailing list was added successfully.'));
        $this->view();
    }

    public function update($id = null)
    {
        $mailingList = $this->entityManager->getRepository(MailingList::class)->findOneBy(["id" => $id]);

        if ($mailingList instanceof MailingList) {
            $factory = $this->createBreadcrumbFactory();

            $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $mailingList));

            if ($this->request->getMethod() === "POST" && $this->validate()) {
                $mailingList->setName($this->request->request->get("name"));
                /** @var Service $siteService */
                $siteService = $this->app->make(Service::class);
                $site = $siteService->getByID($this->request->request->get("siteId"));
                $mailingList->setSite($site);
                $this->entityManager->persist($mailingList);
                $this->entityManager->flush();
                return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/mailing_lists/updated"), Response::HTTP_TEMPORARY_REDIRECT);
            }
        } else {
            return $this->responseFactory->notFound(t("Not Found"));
        }

        $this->set('sites', $this->getSites());
        $this->set('mailingList', $mailingList);

        $this->render("/dashboard/simple_newsletter/mailing_lists/detail", "simple_newsletter");
    }

    public function updated()
    {
        $this->set('success', t('The mailing list was updated successfully.'));
        $this->view();
    }

    public function removed()
    {
        $this->set('success', t('The mailing list was removed successfully.'));
        $this->view();
    }

    public function remove($id = null)
    {
        $mailingList = $this->entityManager->getRepository(MailingList::class)->findOneBy(["id" => $id]);

        if ($mailingList instanceof MailingList) {
            $db = $this->entityManager->getConnection();
            try {
                $db->executeQuery("UPDATE SimpleNewsletterCampaign SET mailingListId = NULL WHERE mailingListId = ?", [$mailingList->getId()]);
            } catch (Exception $e) {
            }
            $this->entityManager->remove($mailingList);
            $this->entityManager->flush();
            return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/mailing_lists/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            return $this->responseFactory->notFound(t("Not Found"));
        }
    }

    public function view()
    {
        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [
            $this->getSearchKeywordsField()
        ]);

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);

        $this->headerSearch->getElementController()->setQuery(null);
    }

}
