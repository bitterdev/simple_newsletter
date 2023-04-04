<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Subscriber\Search\Field\Field;

use Bitter\SimpleNewsletter\Service\MailingList;
use Bitter\SimpleNewsletter\Subscriber\SubscriberList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class SubscribedMailingListField extends AbstractField
{
    protected $requestVariables = [
        'subscribedMailingList',
    ];

    public function getKey()
    {
        return 'subscribedMailingList';
    }

    public function getDisplayName()
    {
        return t('Subscribed Mailing List');
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
        return $form->select('subscribedMailingList', $mailingLists, $this->getData('subscribedMailingList'), ['style' => 'vertical-align: middle']);

    }

    /**
     * @param SubscriberList $list
     */
    public function filterList(ItemList $list)
    {
        if ((int)$this->getData('subscribedMailingList') > 0) {
            $list->filterByMailingList($this->getData('subscribedMailingList'));
        }
    }


}
