<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\MailingList;

use Bitter\SimpleNewsletter\Entity\MailingList;
use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(MailingList $mailingList)
    {
        parent::__construct();

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/simple_newsletter/mailing_lists/update", $mailingList->getId()),
                t('Edit')
            )
        );

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/simple_newsletter/mailing_lists/remove", $mailingList->getId()),
                t('Remove'),
                [
                    "class" => "ccm-delete-item"
                ]
            )
        );
    }
}
