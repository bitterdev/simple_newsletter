<?php /** @noinspection PhpUnused */
/** @noinspection DuplicatedCode */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleNewsletter\Block\UnsubscribeForm;

use Bitter\SimpleNewsletter\Config;
use Bitter\SimpleNewsletter\Form\Service\Validation;
use Bitter\SimpleNewsletter\Service\MailingList as MailingListService;
use Bitter\SimpleNewsletter\Service\Subscribe;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;

class Controller extends BlockController
{
    protected $btTable = "btUnsubscribeForm";
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

    protected $displayCaptcha;

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
        return t('Add unsubscribe newsletter subscribe form.');
    }

    public function getBlockTypeName()
    {
        return t('Unsubscribe Form');
    }

    public function save($args)
    {
        $args["displayCaptcha"] = isset($args["displayCaptcha"]) ? 1 : 0;
        parent::save($args);
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
        $mailingLists = $this->mailingListService->getList();

        $this->set('mailingLists', $mailingLists);
        $this->set('error', $this->error);
        $this->set("success", false);
    }

    public function add()
    {
        $this->set("buttonText", t('Unsubscribe'));
        $this->set("displayCaptcha", true);

        $this->setDefaults();
    }

    public function edit()
    {
        $this->setDefaults();
    }

    public function view()
    {
        $this->setDefaults();
        $this->set("email", $this->request->query->get("email", ""));
    }

    public function action_unsubscribe()
    {
        $this->setDefaults();

        if ($this->request->getMethod() === 'POST') {
            $this->formValidator->setData($this->request->request->all());

            $this->formValidator->addRequiredToken("unsubscribe_newsletter");

            $this->formValidator->addRequiredMailingList("mailingList", t("You need to select a valid mailing list."));

            $this->formValidator->addRequiredEmail("email", t("You need to enter a valid mail address."));

            if (!$this->formValidator->test()) {
                $this->error = $this->formValidator->getError();
            }

            if ($this->displayCaptcha && !$this->captcha->check()) {
                $this->error->add(t("The given captcha is invalid."));
            }

            if (!$this->error->has()) {
                $this->error = $this->subscribeService->unsubscribe(
                    $this->request->request->get("email"),
                    $this->request->request->get("mailingList")
                );

                if (!$this->error->has()) {
                    $this->set('success', t("Thank you. You was successfully removed from the selected mailing lists."));
                }
            }

            $this->set('error', $this->error);
        }
    }

}
