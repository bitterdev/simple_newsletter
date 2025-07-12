<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Concrete\Package\SimpleNewsletter\Controller\SinglePage\Dashboard\SimpleNewsletter;

use Bitter\SimpleNewsletter\Config;
use Bitter\SimpleNewsletter\Form\Service\Validation;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Controller\DashboardSitePageController;

class Settings extends DashboardSitePageController
{
    /** @var Request */
    protected $request;
    /** @var Config */
    protected $config;
    /** @var Validation */
    protected $formValidator;

    public function on_start()
    {
        parent::on_start();

        $this->request = $this->app->make(Request::class);
        $this->config = $this->app->make(Config::class);
        $this->config->setSite($this->getSite());
        $this->formValidator = $this->app->make(Validation::class);
    }

    public function view()
    {
        if ($this->request->getMethod() === 'POST') {
            $this->formValidator->setData($this->request->request->all());

            $this->formValidator->addRequiredToken("update_settings");

            $this->formValidator->addRequired("legalInformation", t("You need to enter your legal informations."));
            $this->formValidator->addRequiredPage("unsubscribePageId", t("You need to select a unsubscription page."));

            if (!$this->formValidator->test()) {
                $this->error = $this->formValidator->getError();
            }

            if (!$this->error->has()) {
                $this->config->setLegalInformation($this->request->request->get("legalInformation"));
                $this->config->setPrivacyPageId($this->request->request->get("privacyPageId"));
                $this->config->setSubscriptionMethod($this->request->request->get("subscriptionMethod"));
                $this->config->setTermsOfUsePageId($this->request->request->get("termsOfUsePageId"));
                $this->config->setUnsubscribePageId($this->request->request->get("unsubscribePageId"));

                $this->set('success', t("The settings has been updated successfully."));
            }
        }

        $this->set('legalInformation', $this->config->getLegalInformation());
        $this->set('privacyPageId', $this->config->getPrivacyPageId());
        $this->set('subscriptionMethod', $this->config->getSubscriptionMethod());
        $this->set('subscriptionMethods', $this->config->getSubscriptionMethods());
        $this->set('termsOfUsePageId', $this->config->getTermsOfUsePageId());
        $this->set('unsubscribePageId', $this->config->getUnsubscribePageId());
    }

}
