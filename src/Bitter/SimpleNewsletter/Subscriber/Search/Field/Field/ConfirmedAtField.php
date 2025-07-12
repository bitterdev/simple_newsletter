<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Subscriber\Search\Field\Field;

use Bitter\SimpleNewsletter\Subscriber\SubscriberList;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Illuminate\Contracts\Container\BindingResolutionException;

class ConfirmedAtField extends AbstractField
{

    protected $requestVariables = [
        'confirmed_at_from_dt',
        'confirmed_at_from_h',
        'confirmed_at_from_m',
        'confirmed_at_from_a',
        'confirmed_at_to_dt',
        'confirmed_at_to_h',
        'confirmed_at_to_m',
        'confirmed_at_to_a',
    ];

    public function getKey()
    {
        return 'confirmed_at';
    }

    public function getDisplayName()
    {
        return t('Confirmed At');
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $wdt */
        /** @noinspection PhpUnhandledExceptionInspection */
        $wdt = $app->make(DateTime::class);
        return $wdt->datetime('confirmed_at_from', $wdt->translate('confirmed_at_from', $this->data)) . t('to') . $wdt->datetime('confirmed_at_to', $wdt->translate('confirmed_at_to', $this->data));

    }

    /**
     * @param SubscriberList $list
     * @throws BindingResolutionException
     */
    public function filterList(ItemList $list)
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $wdt */
        /** @noinspection PhpUnhandledExceptionInspection */
        $wdt = $app->make(DateTime::class);

        $dateFrom = $wdt->translate('confirmed_at_from', $this->data);

        if ($dateFrom) {
            $list->filterByConfirmedAt($dateFrom, '>=');
        }

        $dateTo = $wdt->translate('confirmed_at_to', $this->data);

        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }

            $list->filterByConfirmedAt($dateTo, '<=');
        }
    }



}
