<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Controller;

use Bitter\SimpleNewsletter\Attribute\Category\NewsletterSubscriberCategory;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;

class Api implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $responseFactory;
    /** @var Package|PackageEntity */
    protected $pkg;

    public function __construct(
        ResponseFactory $responseFactory,
        PackageService $packageService
    )
    {
        $this->responseFactory = $responseFactory;
        $this->pkg = $packageService->getByHandle("simple_newsletter");
    }

    public function getPlaceholders()
    {
        $placeholders = [];

        /** @var CategoryService $service */
        $service = $this->app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('newsletter_subscriber');
        /** @var NewsletterSubscriberCategory $category */
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();

        foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
            /** @var \Bitter\SimpleNewsletter\Entity\Attribute\Key\NewsletterSubscriberKey $ak */
            $placeholders[$ak->getAttributeKeyHandle()] = $ak->getAttributeKeyName();
        }

        return $this->responseFactory->json($placeholders, Response::HTTP_OK);
    }

    public function hideReminder()
    {
        $this->pkg->getConfig()->save('reminder.hide', true);
        return $this->responseFactory->create("", Response::HTTP_OK);
    }

    public function hideDidYouKnow()
    {
        $this->pkg->getConfig()->save('did_you_know.hide', true);
        return $this->responseFactory->create("", Response::HTTP_OK);
    }

    public function hideLicenseCheck()
    {
        $this->pkg->getConfig()->save('license_check.hide', true);
        return $this->responseFactory->create("", Response::HTTP_OK);
    }
}