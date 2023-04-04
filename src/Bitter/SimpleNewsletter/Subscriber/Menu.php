<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Subscriber;

use Bitter\SimpleNewsletter\Entity\Subscriber;
use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(Subscriber $subscriber)
    {
        parent::__construct();

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/simple_newsletter/subscribers/update", $subscriber->getId()),
                t('Edit')
            )
        );

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/simple_newsletter/subscribers/remove", $subscriber->getId()),
                t('Remove'),
                [
                    "class" => "ccm-delete-item"
                ]
            )
        );
    }
}
