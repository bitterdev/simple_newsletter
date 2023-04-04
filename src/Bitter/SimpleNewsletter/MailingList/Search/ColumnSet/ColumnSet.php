<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\MailingList\Search\ColumnSet;

use Concrete\Core\Support\Facade\Facade;
use Bitter\SimpleNewsletter\MailingList\Search\SearchProvider;
use Concrete\Core\Search\Column\Set;

class ColumnSet extends Set
{
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
