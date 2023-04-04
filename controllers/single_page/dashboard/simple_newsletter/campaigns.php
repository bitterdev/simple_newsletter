<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleNewsletter\Controller\SinglePage\Dashboard\SimpleNewsletter;

use Bitter\SimpleNewsletter\Entity\Campaign;
use Bitter\SimpleNewsletter\Entity\Search\SavedCampaignSearch;
use Bitter\SimpleNewsletter\Enumeration\CampaignState;
use Bitter\SimpleNewsletter\Form\Service\Validation;
use Bitter\SimpleNewsletter\Campaign\Search\Menu\MenuFactory;
use Bitter\SimpleNewsletter\Campaign\Search\SearchProvider;
use Bitter\SimpleNewsletter\Navigation\Breadcrumb\Dashboard\DashboardCampaignBreadcrumbFactory;
use Bitter\SimpleNewsletter\Service\MailingList;
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
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\SimpleNewsletter\Controller\Element\Dashboard\SimpleNewsletter\Campaigns\Header;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class Campaigns extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var Validation */
    protected $validation;
    /** MailingList */
    protected $mailingListService;
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
            $this->headerMenu = $this->app->make(ElementManager::class)->get('campaigns/search/menu', 'simple_newsletter');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch(): Element
    {
        if (!isset($this->headerSearch)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->headerSearch = $this->app->make(ElementManager::class)->get('campaigns/search/search', 'simple_newsletter');
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
            $preset = $this->entityManager->find(SavedCampaignSearch::class, $presetID);

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
     * @return DashboardCampaignBreadcrumbFactory
     * @throws BindingResolutionException
     */
    protected function createBreadcrumbFactory(): DashboardCampaignBreadcrumbFactory
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(DashboardCampaignBreadcrumbFactory::class);
    }

    public function on_start()
    {
        parent::on_start();
        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->validation = $this->app->make(Validation::class);
        $this->mailingListService = $this->app->make(MailingList::class);
    }

    private function validate()
    {
        $this->validation->setData($this->request->request->all());

        $this->validation->addRequiredToken("update_campaign");
        $this->validation->addRequiredMailingList("mailingList", t("You need to select a valid mailing list."));
        $this->validation->addRequired("name", t("You need to enter a valid name."));
        $this->validation->addRequired("subject", t("You need to enter a valid subject."));
        $this->validation->addRequired("body", t("You need to enter a valid body."));

        if (!$this->validation->test()) {
            $this->error = $this->validation->getError();
            return false;
        } else {
            return true;
        }
    }

    public function add()
    {
        $mailingLists = $this->mailingListService->getList();

        if (count($mailingLists) === 0) {
            return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/campaigns/no_campaigns"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        if ($this->request->getMethod() === "POST" && $this->validate()) {
            /** @var \Bitter\SimpleNewsletter\Entity\MailingList $mailingList */
            $mailingList = $this->entityManager->getRepository(\Bitter\SimpleNewsletter\Entity\MailingList::class)->findOneBy([
                "id" => $this->request->request->get("mailingList")
            ]);

            $campaign = new Campaign();
            $campaign->setCreatedAt(new DateTime());
            $campaign->setState(CampaignState::DRAFT);
            $campaign->setMailingList($mailingList);
            $campaign->setName($this->request->request->get("name"));
            $campaign->setSubject($this->request->request->get("subject"));
            $campaign->setBody($this->request->request->get("body"));
            $this->entityManager->persist($campaign);
            $this->entityManager->flush();
            return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/campaigns/added"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->set('mailingLists', $mailingLists);
        $this->set('campaign', new Campaign());

        $this->render("/dashboard/simple_newsletter/campaigns/detail", "simple_newsletter");
    }

    public function no_campaigns()
    {
        $this->error->add(t("You can't add any campaign because you don't have any mailing lists. Please create a mailing list first."));
        $this->view();
    }

    public function added()
    {
        $this->set('success', t('The campaign was added successfully.'));
        $this->view();
    }

    public function update($id = null)
    {
        $campaign = $this->entityManager->getRepository(Campaign::class)->findOneBy(["id" => $id]);

        if ($campaign instanceof Campaign) {
            if ($campaign->getState() === CampaignState::QUEUED ||
                $campaign->getState() === CampaignState::SENT) {
                return $this->responseFactory->forbidden((string)Url::to("/dashboard/simple_newsletter/campaigns"));
            }

            if ($this->request->getMethod() === "POST" && $this->validate()) {
                /** @var \Bitter\SimpleNewsletter\Entity\MailingList $mailingList */
                $mailingList = $this->entityManager->getRepository(\Bitter\SimpleNewsletter\Entity\MailingList::class)->findOneBy([
                    "id" => $this->request->request->get("mailingList")
                ]);

                $campaign->setName($this->request->request->get("name"));
                $campaign->setSubject($this->request->request->get("subject"));
                $campaign->setMailingList($mailingList);
                $campaign->setBody($this->request->request->get("body"));
                $this->entityManager->persist($campaign);
                $this->entityManager->flush();
                return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/campaigns/updated"), Response::HTTP_TEMPORARY_REDIRECT);
            }
        } else {
            return $this->responseFactory->notFound(t("Not Found"));
        }

        $this->set('mailingLists', $this->mailingListService->getList());
        $this->set('campaign', $campaign);

        $this->render("/dashboard/simple_newsletter/campaigns/detail", "simple_newsletter");
    }

    public function updated()
    {
        $this->set('success', t('The campaign was updated successfully.'));
        $this->view();
    }

    public function duplicated()
    {
        $this->set('success', t('The campaign was duplicated successfully.'));
        $this->view();
    }

    public function removed()
    {
        $this->set('success', t('The campaign was removed successfully.'));
        $this->view();
    }

    public function added_to_queue()
    {
        $this->set('success', t('The campaign was successfully added to the send queue. Navigate to the dashboard page automated jobs and run the send newsletters job to send the campaign to all subscribers of the assosiated mailing list.'));
        $this->view();
    }

    public function add_to_queue($id = null)
    {
        $campaign = $this->entityManager->getRepository(Campaign::class)->findOneBy(["id" => $id]);

        if ($campaign instanceof Campaign) {
            $campaign->setState(CampaignState::QUEUED);
            $this->entityManager->persist($campaign);
            $this->entityManager->flush();
            return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/campaigns/added_to_queue"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            return $this->responseFactory->notFound(t("Not Found"));
        }
    }

    public function duplicate($id = null)
    {
        $campaign = $this->entityManager->getRepository(Campaign::class)->findOneBy(["id" => $id]);

        if ($campaign instanceof Campaign) {
            $newCampaign = new Campaign();
            $newCampaign->setCreatedAt(new DateTime());
            $newCampaign->setState(CampaignState::DRAFT);
            $newCampaign->setSubject($campaign->getSubject());
            $newCampaign->setMailingList($campaign->getMailingList());
            $newCampaign->setName($campaign->getName());
            $newCampaign->setBody($campaign->getBody());

            $this->entityManager->persist($newCampaign);
            $this->entityManager->flush();

            return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/campaigns/duplicated"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            return $this->responseFactory->notFound(t("Not Found"));
        }
    }

    public function remove($id = null)
    {
        $campaign = $this->entityManager->getRepository(Campaign::class)->findOneBy(["id" => $id]);

        if ($campaign instanceof Campaign) {
            $this->entityManager->remove($campaign);
            $this->entityManager->flush();
            return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/campaigns/removed"), Response::HTTP_TEMPORARY_REDIRECT);
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
