<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Campaign\Search\Field\Field;

use Bitter\SimpleNewsletter\Enumeration\CampaignState;
use Bitter\SimpleNewsletter\Campaign\CampaignList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class StateField extends AbstractField
{
    protected $requestVariables = [
        'state',
    ];

    public function getKey()
    {
        return 'state';
    }

    public function getDisplayName()
    {
        return t('State');
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);

        $states = [
            CampaignState::SENT => t("Sent"),
            CampaignState::DRAFT => t("Draft"),
            CampaignState::QUEUED => t("Queued")
        ];

        return $form->select('state', $states, $this->getData('state'), ['style' => 'vertical-align: middle']);

    }

    /**
     * @param CampaignList $list
     */
    public function filterList(ItemList $list)
    {
        if (strlen($this->getData('state')) > 0) {
            $list->filterByState($this->getData('state'));
        }
    }


}
