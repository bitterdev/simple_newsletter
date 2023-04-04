<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Campaign\Search\Field\Field;

use Bitter\SimpleNewsletter\Campaign\CampaignList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class SubjectField extends AbstractField
{
    protected $requestVariables = [
        'subject'
    ];

    public function getKey()
    {
        return 'subject';
    }

    public function getDisplayName()
    {
        return t('Subject');
    }

    /**
     * @param CampaignList $list
     */
    public function filterList(ItemList $list)
    {
        $list->filterBySubject($this->getData('subject'));
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->text('subject' , $this->getData('subject'));
    }
}
