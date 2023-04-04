<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Subscriber\Search\Field\Field;

use Bitter\SimpleNewsletter\Subscriber\SubscriberList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class ConfirmedField extends AbstractField
{
    protected $requestVariables = [
        'confirmed',
    ];

    public function getKey()
    {
        return 'confirmed';
    }

    public function getDisplayName()
    {
        return t('Confirmed');
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->select('confirmed', [
            '0' => t('Unconfirmed Subscribers'),
            '1' => t('Confirmed Subscribers')
        ], $this->getData('confirmed'), ['style' => 'vertical-align: middle']);

    }

    /**
     * @param SubscriberList $list
     */
    public function filterList(ItemList $list)
    {
        if ($this->getData('confirmed') === '0') {
            $list->filterByConfirmed(0);
        } elseif ($this->getData('confirmed') === '1') {
            $list->filterByConfirmed(1);
        }
    }


}
