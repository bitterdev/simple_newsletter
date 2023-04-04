<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Attribute\Key;

use Bitter\SimpleNewsletter\Attribute\Category\NewsletterSubscriberCategory;
use Concrete\Core\Support\Facade\Facade;

class NewsletterSubscriberKey extends Facade
{
    public static function getFacadeAccessor()
    {
        return NewsletterSubscriberCategory::class;
    }

    public static function getByHandle($handle)
    {
        return static::getFacadeRoot()->getAttributeKeyByHandle($handle);
    }

    public static function getByID($akID)
    {
        return static::getFacadeRoot()->getAttributeKeyByID($akID);
    }
}
