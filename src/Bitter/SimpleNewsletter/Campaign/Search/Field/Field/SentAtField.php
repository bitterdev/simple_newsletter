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

class SentAtField extends AbstractField
{
    protected $requestVariables = [
        'sent_at_from_dt',
        'sent_at_from_h',
        'sent_at_from_m',
        'sent_at_from_a',
        'sent_at_to_dt',
        'sent_at_to_h',
        'sent_at_to_m',
        'sent_at_to_a',
    ];

    public function getKey()
    {
        return 'sent_at';
    }

    public function getDisplayName()
    {
        return t('Sent At');
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $wdt */
        /** @noinspection PhpUnhandledExceptionInspection */
        $wdt = $app->make(DateTime::class);
        return $wdt->datetime('sent_at_from', $wdt->translate('sent_at_from', $this->data)) . t('to') . $wdt->datetime('sent_at_to', $wdt->translate('sent_at_to', $this->data));

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

        $dateFrom = $wdt->translate('sent_at_from', $this->data);

        if ($dateFrom) {
            $list->filterBySentAt($dateFrom, '>=');
        }

        $dateTo = $wdt->translate('sent_at_to', $this->data);

        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }

            $list->filterBySentAt($dateTo, '<=');
        }
    }



}
