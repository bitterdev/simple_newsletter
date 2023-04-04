<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Attribute\Category;

use Concrete\Core\Attribute\Category\Manager as CoreManager;

class Manager extends CoreManager
{
    public function createNewsletterSubscriberDriver()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->app->make(NewsletterSubscriberCategory::class);
    }
}
