<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleNewsletter\Controller\Element\Dashboard\SimpleNewsletter\Campaigns;

use Concrete\Core\Controller\ElementController;

class Header extends ElementController
{
    protected $pkgHandle = 'simple_newsletter';

    public function getElement()
    {
        return 'dashboard/simple_newsletter/campaigns/header';
    }

}
