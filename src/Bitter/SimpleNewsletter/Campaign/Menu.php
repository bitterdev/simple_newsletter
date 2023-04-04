<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Campaign;

use Bitter\SimpleNewsletter\Entity\Campaign;
use Bitter\SimpleNewsletter\Enumeration\CampaignState;
use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(Campaign $campaign)
    {
        parent::__construct();

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/simple_newsletter/campaigns/add_to_queue", $campaign->getId()),
                t('Add to queue')
            )
        );

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/simple_newsletter/campaigns/duplicate", $campaign->getId()),
                t('Duplicate')
            )
        );

        if ($campaign->getState() == CampaignState::DRAFT) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to("/dashboard/simple_newsletter/campaigns/update", $campaign->getId()),
                    t('Edit')
                )
            );
        }

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/simple_newsletter/campaigns/remove", $campaign->getId()),
                t('Remove'),
                [
                    "class" => "ccm-delete-item"
                ]
            )
        );
    }
}
