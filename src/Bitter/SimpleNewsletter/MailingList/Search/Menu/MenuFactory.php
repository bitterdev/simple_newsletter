<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\MailingList\Search\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;

class MenuFactory
{

    public function createBulkMenu(): DropdownMenu
    {
        $menu = new DropdownMenu();

        $menu->addItem(
            new LinkItem(
                "#",
                t('Delete'),
                [
                    'data-bulk-action-type' => 'dialog',
                    'data-bulk-action-title' => t('Delete'),
                    'data-bulk-action-url' => Url::to('/ccm/system/dialogs/mailing_lists/bulk/delete'),
                    'data-bulk-action-dialog-width' => '630',
                    'data-bulk-action-dialog-height' => '450'
                ]
            )
        );

        return $menu;
    }

}
