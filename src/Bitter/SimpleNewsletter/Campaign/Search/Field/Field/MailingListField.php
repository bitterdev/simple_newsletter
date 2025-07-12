<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Campaign\Search\Field\Field;

use Bitter\SimpleNewsletter\Service\MailingList;
use Bitter\SimpleNewsletter\Campaign\CampaignList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class MailingListField extends AbstractField
{
    protected $requestVariables = [
        'mailingList',
    ];

    public function getKey()
    {
        return 'mailingList';
    }

    public function getDisplayName()
    {
        return t('Mailing List');
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        /** @var MailingList $mailingListService */
        $mailingListService = $app->make(MailingList::class);
        $mailingLists = $mailingListService->getList();
        return $form->select('mailingList', $mailingLists, $this->getData('mailingList'), ['style' => 'vertical-align: middle']);

    }

    /**
     * @param CampaignList $list
     */
    public function filterList(ItemList $list)
    {
        if ((int)$this->getData('mailingList') > 0) {
            $list->filterByMailingList($this->getData('mailingList'));
        }
    }


}
