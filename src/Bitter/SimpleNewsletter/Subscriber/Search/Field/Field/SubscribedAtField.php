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

class SubscribedAtField extends AbstractField
{

    protected $requestVariables = [
        'subscribed_at_from_dt',
        'subscribed_at_from_h',
        'subscribed_at_from_m',
        'subscribed_at_from_a',
        'subscribed_at_to_dt',
        'subscribed_at_to_h',
        'subscribed_at_to_m',
        'subscribed_at_to_a',
    ];

    public function getKey()
    {
        return 'subscribed_at';
    }

    public function getDisplayName()
    {
        return t('Subscribed At');
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $wdt */
        /** @noinspection PhpUnhandledExceptionInspection */
        $wdt = $app->make(DateTime::class);
        return $wdt->datetime('subscribed_at_from', $wdt->translate('subscribed_at_from', $this->data)) . t('to') . $wdt->datetime('subscribed_at_to', $wdt->translate('subscribed_at_to', $this->data));

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

        $dateFrom = $wdt->translate('subscribed_at_from', $this->data);

        if ($dateFrom) {
            $list->filterBySubscribedAt($dateFrom, '>=');
        }

        $dateTo = $wdt->translate('subscribed_at_to', $this->data);

        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }

            $list->filterBySubscribedAt($dateTo, '<=');
        }
    }



}
