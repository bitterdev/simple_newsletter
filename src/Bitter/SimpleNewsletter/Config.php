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
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Service;

class Config implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var Site */
    protected $site;

    public function __construct(
        Service $siteService
    )
    {
        $this->site = $siteService->getSite();
    }

    /**
     * @return Site
     */
    public function getSite(): ?Site
    {
        return $this->site;
    }

    /**
     * @param Site $site
     * @return Config
     */
    public function setSite(?Site $site): Config
    {
        $this->site = $site;
        return $this;
    }

    public function getPrivacyPage()
    {
        return Page::getByID($this->getPrivacyPageId());
    }

    public function getPrivacyPageId()
    {
        return (int)$this->site->getConfigRepository()->get("simple_newsletter.privacy_page_id");
    }

    public function setPrivacyPageId($privacyPageId)
    {
        $this->site->getConfigRepository()->save("simple_newsletter.privacy_page_id", (int)$privacyPageId);
    }

    public function getTermsOfUsePage()
    {
        return Page::getByID($this->getTermsOfUsePageId());
    }

    public function getTermsOfUsePageId()
    {
        return (int)$this->site->getConfigRepository()->get("simple_newsletter.terms_of_use_page_id");
    }

    public function setTermsOfUsePageId($termsOfUsePageId)
    {
        $this->site->getConfigRepository()->save("simple_newsletter.terms_of_use_page_id", (int)$termsOfUsePageId);
    }

    public function getSubscriptionMethods()
    {
        return [
            SubscriptionMethod::SINGLE_OPT_IN => t("Single-Opt-In"),
            SubscriptionMethod::DOUBLE_OPT_IN => t("Double-Opt-In")
        ];
    }

    public function getSubscriptionMethod()
    {
        return (string)$this->site->getConfigRepository()->get("simple_newsletter.subscription_method", SubscriptionMethod::DOUBLE_OPT_IN);
    }

    public function setSubscriptionMethod($subscriptionMethod)
    {
        $this->site->getConfigRepository()->save("simple_newsletter.subscription_method", (string)$subscriptionMethod);
    }

    public function getUnsubscribePage()
    {
        return Page::getByID($this->getUnsubscribePageId());
    }

    public function getUnsubscribePageId()
    {
        return (int)$this->site->getConfigRepository()->get("simple_newsletter.unsubscribe_page_id");
    }

    public function setUnsubscribePageId($unsubscribePageId)
    {
        $this->site->getConfigRepository()->save("simple_newsletter.unsubscribe_page_id", (int)$unsubscribePageId);
    }

    public function getLegalInformation()
    {
        return (string)$this->site->getConfigRepository()->get("simple_newsletter.legal_information");
    }

    public function setLegalInformation($legalInformation)
    {
        $this->site->getConfigRepository()->save("simple_newsletter.legal_information", $legalInformation);
    }

}