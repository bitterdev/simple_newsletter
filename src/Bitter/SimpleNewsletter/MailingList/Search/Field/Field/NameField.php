<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\MailingList\Search\Field\Field;

use Bitter\SimpleNewsletter\MailingList\MailingList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class NameField extends AbstractField
{
    protected $requestVariables = [
        'name'
    ];

    public function getKey()
    {
        return 'email';
    }

    public function getDisplayName()
    {
        return t('Name');
    }

    /**
     * @param MailingList $list
     */
    public function filterList(ItemList $list)
    {
        $list->filterByName($this->getData('name'));
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->text('name' , $this->getData('name'));
    }
}
