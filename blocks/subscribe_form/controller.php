<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Concrete\Package\SimpleNewsletter\Block\SubscribeForm;

use Bitter\SimpleNewsletter\Attribute\Category\NewsletterSubscriberCategory;
use Bitter\SimpleNewsletter\Config;
use Bitter\SimpleNewsletter\Entity\Attribute\Key\NewsletterSubscriberKey;
use Bitter\SimpleNewsletter\Enumeration\SubscriptionMethod;
use Bitter\SimpleNewsletter\Form\Service\Validation;
use Bitter\SimpleNewsletter\Service\MailingList as MailingListService;
use Bitter\SimpleNewsletter\Service\Subscribe;
use Bitter\SimpleNewsletter\Subscriber\SubscriberInfo;
use Bitter\SimpleNewsletter\Subscriber\SubscriberInfoRepository;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Context\FrontendFormContext;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;

class Controller extends BlockController
{
    protected $btTable = "btSubscribeForm";
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var MailingListService */
    protected $mailingListService;
    /** @var Request */
    protected $request;
    /** @var Validation */
    protected $formValidator;
    /** @var ErrorList */
    protected $error;
    /** @var CaptchaInterface */
    protected $captcha;
    /** @var Config */
    protected $config;
    /** @var Subscribe */
    protected $subscribeService;

    protected $displayTermsOfUse;
    protected $displayCaptcha;
    protected $displayPrivacy;

    public function on_start()
    {
        parent::on_start();

        $this->request = $this->app->make(Request::class);
        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->mailingListService = $this->app->make(MailingListService::class);
        $this->formValidator = $this->app->make(Validation::class);
        $this->error = $this->app->make(ErrorList::class);
        $this->captcha = $this->app->make(CaptchaInterface::class);
        $this->config = $this->app->make(Config::class);
        $this->subscribeService = $this->app->make(Subscribe::class);
    }

    public function getBlockTypeDescription()
    {
        return t('Add subscribe newsletter subscribe form.');
    }

    public function getBlockTypeName()
    {
        return t('Subscribe Form');
    }

    public function validate($args)
    {
        /** @var Validation $validationService */
        $validationService = $this->app->make(Validation::class);

        $validationService->setData($args);

        $validationService->addRequired("buttonText", t("You need to enter a valid button text."));

        $validationService->test();

        return $validationService->getError();
    }

    public function setDefaults()
    {
        $mailingLists = $this->mailingListService->getListByCurrentSite();

        $this->set('mailingLists', $mailingLists);
        $this->set('error', $this->error);
        $this->set('success', false);
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
    }

    public function add()
    {
        $this->set("buttonText", t('Subscribe'));
        $this->set("displayCaptcha", true);
        $this->set("displayTermsOfUse", true);
        $this->set("displayPrivacy", true);

        $this->setDefaults();
    }

    public function edit()
    {
        $this->setDefaults();
    }

    public function view()
    {
        $this->setDefaults();
    }

    public function action_confirm_subscription()
    {
        $this->setDefaults();

        $this->error = $this->subscribeService->confirm(
            $this->request->query->get("confirmationHash")
        );

        if (!$this->error->has()) {
            $this->set('success', t("Thank you. Your newsletter subscription was successfully confirmed."));
        } else {
            $this->set('error', $this->error);
        }
    }

    public function save($args)
    {
        $args["displayCaptcha"] = isset($args["displayCaptcha"]) ? 1 : 0;
        $args["displayTermsOfUse"] = isset($args["displayTermsOfUse"]) ? 1 : 0;
        $args["displayPrivacy"] = isset($args["displayPrivacy"]) ? 1 : 0;
        parent::save($args);
    }

    public function action_subscribe()
    {
        $this->setDefaults();

        if ($this->request->getMethod() === 'POST') {
            $this->formValidator->setData($this->request->request->all());

            $this->formValidator->addRequiredToken("subscribe_newsletter");

            $this->formValidator->addRequiredMailingList("mailingList", t("You need to select a valid mailing list."));

            $this->formValidator->addRequiredEmail("email", t("You need to enter a valid mail address."));

            if ($this->displayPrivacy) {
                $this->formValidator->addRequired("acceptPrivacy", t("You need to accept the privacy."));
            }

            if ($this->displayTermsOfUse) {
                $this->formValidator->addRequired("acceptTermsOfUse", t("You need to accept the terms of use."));
            }

            if (!$this->formValidator->test()) {
                $this->error = $this->formValidator->getError();
            }

            if ($this->displayCaptcha && !$this->captcha->check()) {
                $this->error->add(t("The given captcha is invalid."));
            }

            if (!$this->error->has()) {
                $this->error = $this->subscribeService->subscribe(
                    $this->request->request->get("email"),
                    $this->request->request->get("mailingList"),
                    $this->getActionURL("confirm_subscription")
                );

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
                        $this->request,
                        true
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
                }

                if (!$this->error->has()) {
                    if ($this->config->getSubscriptionMethod() === SubscriptionMethod::DOUBLE_OPT_IN) {
                        $this->set('success', t("Thank you for your subscription. Please check your email inbox to confirm the subscription."));
                    } else {
                        $this->set('success', t("Thank you for your subscription. You have been successfully subscribed to the newsletter."));
                    }
                }
            }

            $this->set('error', $this->error);
        }
    }

}
