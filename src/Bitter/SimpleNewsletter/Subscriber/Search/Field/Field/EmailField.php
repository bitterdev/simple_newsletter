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

class EmailField extends AbstractField
{
    protected $requestVariables = [
        'email'
    ];

    public function getKey()
    {
        return 'email';
    }

    public function getDisplayName()
    {
        return t('Email');
    }

    /**
     * @param SubscriberList $list
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByEmail($this->getData('email'));
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->text('email' , $this->getData('email'));
    }
}
