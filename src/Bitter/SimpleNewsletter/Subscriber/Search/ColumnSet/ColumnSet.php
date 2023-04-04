<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet;

use Bitter\SimpleNewsletter\Attribute\Key\NewsletterSubscriberKey;
use Bitter\SimpleNewsletter\Subscriber\Search\ColumnSet\Column\SubscriberAttributeKeyColumn;
use Concrete\Core\Support\Facade\Facade;
use Bitter\SimpleNewsletter\Subscriber\Search\SearchProvider;
use Concrete\Core\Search\Column\Set;

class ColumnSet extends Set
{
    protected $attributeClass = NewsletterSubscriberKey::class;

    public function getAttributeKeyColumn($akHandle)
    {
        $ak = call_user_func([$this->attributeClass, 'getByHandle'], $akHandle);
        return new SubscriberAttributeKeyColumn($ak);
    }

    public static function getCurrent()
    {
        $app = Facade::getFacadeApplication();

        /**
         * @var $provider SearchProvider
         */
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider = $app->make(SearchProvider::class);
        $query = $provider->getSessionCurrentQuery();

        if ($query) {
            return $query->getColumns();
        }

        return new DefaultSet();
    }
}
