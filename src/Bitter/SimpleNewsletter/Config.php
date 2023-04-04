<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter;

use Bitter\SimpleNewsletter\Enumeration\SubscriptionMethod;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Page;

class Config implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $config;

    public function __construct(
        Repository $config
    )
    {
        $this->config = $config;
    }

    public function getPrivacyPage()
    {
        return Page::getByID($this->getPrivacyPageId());
    }

    public function getPrivacyPageId()
    {
        return (int)$this->config->get("simple_newsletter.privacy_page_id");
    }

    public function setPrivacyPageId($privacyPageId)
    {
        $this->config->save("simple_newsletter.privacy_page_id", (int)$privacyPageId);
    }

    public function getTermsOfUsePage()
    {
        return Page::getByID($this->getTermsOfUsePageId());
    }

    public function getTermsOfUsePageId()
    {
        return (int)$this->config->get("simple_newsletter.terms_of_use_page_id");
    }

    public function setTermsOfUsePageId($termsOfUsePageId)
    {
        $this->config->save("simple_newsletter.terms_of_use_page_id", (int)$termsOfUsePageId);
    }

    public function getSubscriptionMethods() {
        return [
            SubscriptionMethod::SINGLE_OPT_IN => t("Single-Opt-In"),
            SubscriptionMethod::DOUBLE_OPT_IN => t("Double-Opt-In")
        ];
    }

    public function getSubscriptionMethod()
    {
        return (string)$this->config->get("simple_newsletter.subscription_method", SubscriptionMethod::DOUBLE_OPT_IN);
    }

    public function setSubscriptionMethod($subscriptionMethod)
    {
        $this->config->save("simple_newsletter.subscription_method", (string)$subscriptionMethod);
    }

    public function getUnsubscribePage()
    {
        return Page::getByID($this->getUnsubscribePageId());
    }

    public function getUnsubscribePageId()
    {
        return (int)$this->config->get("simple_newsletter.unsubscribe_page_id");
    }

    public function setUnsubscribePageId($unsubscribePageId)
    {
        $this->config->save("simple_newsletter.unsubscribe_page_id", (int)$unsubscribePageId);
    }

    public function getLegalInformation()
    {
        return (string)$this->config->get("simple_newsletter.legal_information");
    }

    public function setLegalInformation($legalInformation)
    {
        $this->config->save("simple_newsletter.legal_information", $legalInformation);
    }

}