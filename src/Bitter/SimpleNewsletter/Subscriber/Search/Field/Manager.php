<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpMissingFieldTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Subscriber\Search\Field;

use Bitter\SimpleNewsletter\Attribute\Category\NewsletterSubscriberCategory;
use Bitter\SimpleNewsletter\Subscriber\Search\Field\Field\ConfirmedAtField;
use Bitter\SimpleNewsletter\Subscriber\Search\Field\Field\ConfirmedField;
use Bitter\SimpleNewsletter\Subscriber\Search\Field\Field\EmailField;
use Bitter\SimpleNewsletter\Subscriber\Search\Field\Field\SubscribedMailingListField;
//use Concrete\Core\Attribute\Category\CategoryService;
//use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Bitter\SimpleNewsletter\Subscriber\Search\Field\Field\SubscribedAtField;
//use Concrete\Core\Support\Facade\Application;

class Manager extends FieldManager
{
    protected $subscriberCategory;

    public function __construct(NewsletterSubscriberCategory $subscriberCategory)
    {
        $this->subscriberCategory = $subscriberCategory;

        $this->addGroup(t('Core Properties'), [
            new KeywordsField(),
            new EmailField(),
            new SubscribedAtField(),
            new ConfirmedAtField(),
            new ConfirmedField(),
            new SubscribedMailingListField()
        ]);

        // Currently it is not possible to search for attributes from an custom attribute catory
        // because there is a session issue in the core that is running code before package
        // on_start is executed.

        /*
        $app = Application::getFacadeApplication();
        $service = $app->make(CategoryService::class);
        $setManager = $service->getByHandle('newsletter_subscriber')->getController()->getSetManager();
        $attributeSets = $setManager->getAttributeSets();
        $unassigned = $setManager->getUnassignedAttributeKeys();

        foreach ($attributeSets as $set) {
            $attributes = [];

            foreach ($set->getAttributeKeys() as $key) {
                $field = new AttributeKeyField($key);
                $attributes[] = $field;
            }

            $this->addGroup($set->getAttributeSetDisplayName(), $attributes);
        }

        $attributes = [];

        foreach ($unassigned as $key) {
            $field = new AttributeKeyField($key);
            $attributes[] = $field;
        }

        $this->addGroup(t('Other Attributes'), $attributes);
        */
    }
}
