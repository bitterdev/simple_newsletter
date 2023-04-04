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
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Illuminate\Contracts\Container\BindingResolutionException;

class CreatedAtField extends AbstractField
{
    protected $requestVariables = [
        'created_at_from_dt',
        'created_at_from_h',
        'created_at_from_m',
        'created_at_from_a',
        'created_at_to_dt',
        'created_at_to_h',
        'created_at_to_m',
        'created_at_to_a',
    ];

    public function getKey()
    {
        return 'created_at';
    }

    public function getDisplayName()
    {
        return t('Created At');
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $wdt */
        /** @noinspection PhpUnhandledExceptionInspection */
        $wdt = $app->make(DateTime::class);
        return $wdt->datetime('created_at_from', $wdt->translate('created_at_from', $this->data)) . t('to') . $wdt->datetime('created_at_to', $wdt->translate('created_at_to', $this->data));

    }

    /**
     * @param CampaignList $list
     * @throws BindingResolutionException
     */
    public function filterList(ItemList $list)
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $wdt */
        /** @noinspection PhpUnhandledExceptionInspection */
        $wdt = $app->make(DateTime::class);

        $dateFrom = $wdt->translate('created_at_from', $this->data);

        if ($dateFrom) {
            $list->filterByCreatedAt($dateFrom, '>=');
        }

        $dateTo = $wdt->translate('created_at_to', $this->data);

        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }

            $list->filterByCreatedAt($dateTo, '<=');
        }
    }



}
