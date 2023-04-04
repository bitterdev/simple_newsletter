<?php /** @noinspection PhpInconsistentReturnPointsInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpClassConstantAccessedViaChildClassInspection */
/** @noinspection DuplicatedCode */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleNewsletter\Controller\SinglePage\Dashboard\SimpleNewsletter;

use Bitter\SimpleNewsletter\Attribute\Category\NewsletterSubscriberCategory;
use Bitter\SimpleNewsletter\Entity\Attribute\Key\NewsletterSubscriberKey;
use Bitter\SimpleNewsletter\Entity\Search\SavedSubscriberSearch;
use Bitter\SimpleNewsletter\Entity\Subscriber;
use Bitter\SimpleNewsletter\Form\Service\Validation;
use Bitter\SimpleNewsletter\Navigation\Breadcrumb\Dashboard\DashboardSubscriberBreadcrumbFactory;
use Bitter\SimpleNewsletter\Service\MailingList;
use Bitter\SimpleNewsletter\Subscriber\Search\Menu\MenuFactory;
use Bitter\SimpleNewsletter\Subscriber\Search\SearchProvider;
use Bitter\SimpleNewsletter\Subscriber\SubscriberInfo;
use Bitter\SimpleNewsletter\Subscriber\SubscriberInfoRepository;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Context\FrontendFormContext;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

class Subscribers extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var Validation */
    protected $validation;
    /** @var Subscriber|false */
    protected $subscriber = false;
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
            $this->headerMenu = $this->app->make(ElementManager::class)->get('subscribers/search/menu', 'simple_newsletter');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch(): Element
    {
        if (!isset($this->headerSearch)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->headerSearch = $this->app->make(ElementManager::class)->get('subscribers/search/search', 'simple_newsletter');
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
            $preset = $this->entityManager->find(SavedSubscriberSearch::class, $presetID);

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
     * @return DashboardSubscriberBreadcrumbFactory
     * @throws BindingResolutionException
     */
    protected function createBreadcrumbFactory(): DashboardSubscriberBreadcrumbFactory
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(DashboardSubscriberBreadcrumbFactory::class);
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function on_start()
    {
        parent::on_start();
        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->validation = $this->app->make(Validation::class);
    }

    public function removed()
    {
        $this->set('success', t('The subscriber has been removed successfully.'));
        $this->view();
    }

    public function remove($id = null)
    {
        $subscriber = $this->entityManager->getRepository(Subscriber::class)->findOneBy(["id" => $id]);

        if ($subscriber instanceof Subscriber) {
            $db = $this->entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($subscriber->getMailingLists() as $mailingList) {
                $mailingList->removeSubscriber($subscriber);
                $subscriber->removeMailingList($mailingList);
                $this->entityManager->persist($mailingList);
                $this->entityManager->flush();
            }

            $this->entityManager->remove($subscriber);
            $this->entityManager->flush();

            $db->executeQuery("SET foreign_key_checks = 1");

            return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/subscribers/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            return $this->responseFactory->notFound(t("Not Found"));
        }
    }

    private function validate(): bool
    {
        $this->validation->setData($this->request->request->all());

        $this->validation->addRequiredToken("update_subscriber");
        $this->validation->addRequired("email", t("You need to enter a valid mail address."));

        if (!$this->validation->test()) {
            $this->error = $this->validation->getError();
            return false;
        } else {
            return true;
        }
    }

    public function updated()
    {
        $this->set('success', t('The subscriber has been updated successfully.'));
        $this->view();
    }

    public function added()
    {
        $this->set('success', t('The subscriber has been added successfully.'));
        $this->view();
    }

    /**
     * @param Subscriber $subscriber
     * @return bool
     */
    private function save(Subscriber $subscriber)
    {
        /** @var DateTime $dateTimeWidget */
        $dateTimeWidget = $this->app->make(DateTime::class);
        /** @var MailingList $mailingListService */
        $mailingListService = $this->app->make(MailingList::class);
        $confirmedAt = $dateTimeWidget->translate("confirmedAt", $this->request->request->all(), true);
        $subscribedAt = $dateTimeWidget->translate("subscribedAt", $this->request->request->all(), true);

        $subscriber->setEmail($this->request->request->get("email"));
        $subscriber->setConfirmedAt($confirmedAt);
        $subscriber->setSubscribedAt($subscribedAt);
        $subscriber->setIsConfirmed($this->request->request->has("isConfirmed"));
        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();

        foreach ($subscriber->getMailingLists() as $mailingList) {
            $mailingList->removeSubscriber($subscriber);
            $subscriber->removeMailingList($mailingList);
            $this->entityManager->persist($mailingList);
            $this->entityManager->flush();
        }

        if (is_array($this->request->request->get("subscribedMailingLists")) &&
            count($this->request->request->get("subscribedMailingLists")) > 0) {
            foreach ($this->request->request->get("subscribedMailingLists") as $subscribedMailingListId) {
                $mailingList = $mailingListService->getMailingListById((int)$subscribedMailingListId);
                $mailingList->addSubscriber($subscriber);
                $this->entityManager->persist($mailingList);
                $this->entityManager->flush();
            }
        }

        /** @var CategoryService $service */
        $service = $this->app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('newsletter_subscriber');
        /** @var NewsletterSubscriberCategory $category */
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();
        /** @var NewsletterSubscriberKey[] $attributes */
        $attributes = [];

        foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
            $attributes[] = $ak;
        }

        foreach ($attributes as $uak) {
            $controller = $uak->getController();
            $validator = $controller->getValidator();
            $response = $validator->validateSaveValueRequest(
                $controller,
                $this->request
            );

            if (!$response->isValid()) {
                $error = $response->getErrorObject();
                $this->error->add($error);
            }
        }

        if (!$this->error->has()) {
            /** @var SubscriberInfoRepository $subscriberInfoRepository */
            $subscriberInfoRepository = $this->app->make(SubscriberInfoRepository::class);

            $subscriberInfo = $subscriberInfoRepository->getByEmail($this->request->request->get("email"));

            if ($subscriberInfo instanceof SubscriberInfo) {
                $subscriberInfo->saveUserAttributesForm($attributes);
            }

            return true;
        }

        return false;
    }

    public function add()
    {
        $subscriber = new Subscriber();

        if ($this->request->getMethod() === "POST" && $this->validate()) {
            if ($this->save($subscriber)) {
                return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/subscribers/added"), \Symfony\Component\HttpFoundation\Response::HTTP_TEMPORARY_REDIRECT);
            } else {
                $this->entityManager->remove($subscriber);
                $this->entityManager->flush();
            }
        }

        $this->set('subscriber', $subscriber);
        $this->set('renderer', new Renderer(new FrontendFormContext()));

        /** @var CategoryService $service */
        $service = $this->app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('newsletter_subscriber');
        /** @var NewsletterSubscriberCategory $category */
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();
        /** @var NewsletterSubscriberKey[] $attributes */
        $attributes = [];

        foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
            $attributes[] = $ak;
        }

        $this->set('attributes', $attributes);

        $this->render("/dashboard/simple_newsletter/subscribers/detail", "simple_newsletter");
    }

    /**
     * @throws BindingResolutionException
     */
    public function update($id = null)
    {
        $subscriber = $this->entityManager->getRepository(Subscriber::class)->findOneBy(["id" => $id]);

        if ($subscriber instanceof Subscriber) {
            $factory = $this->createBreadcrumbFactory();

            $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $subscriber));

            if ($this->request->getMethod() === "POST" && $this->validate()) {
                if ($this->save($subscriber)) {
                    return $this->responseFactory->redirect((string)Url::to("/dashboard/simple_newsletter/subscribers/updated"), \Symfony\Component\HttpFoundation\Response::HTTP_TEMPORARY_REDIRECT);
                }
            }
        } else {
            return $this->responseFactory->notFound(t("Not Found"));
        }

        $this->set('subscriber', $subscriber);
        $this->set('renderer', new Renderer(new FrontendFormContext(), $subscriber));

        /** @var CategoryService $service */
        $service = $this->app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('newsletter_subscriber');
        /** @var NewsletterSubscriberCategory $category */
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();
        /** @var NewsletterSubscriberKey[] $attributes */
        $attributes = [];

        foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
            $attributes[] = $ak;
        }

        $this->set('attributes', $attributes);

        $this->render("/dashboard/simple_newsletter/subscribers/detail", "simple_newsletter");
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
