<?php /** @noinspection PhpUnused */
/** @noinspection DuplicatedCode */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Concrete\Package\SimpleNewsletter\Controller\Element\Subscribers\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Illuminate\Contracts\Container\BindingResolutionException;

class Search extends ElementController
{
    /**
     * This is where the header search bar in the page should point. This search bar allows keyword searching in
     * different contexts. Valid options are `view` and `folder`.
     *
     * @var string
     */
    protected $headerSearchAction;
    protected $query;

    public function getElement(): string
    {
        return 'subscribers/search/search';
    }

    public function setQuery(Query $query = null): void
    {
        $this->query = $query;
    }

    public function setHeaderSearchAction(string $headerSearchAction): void
    {
        $this->headerSearchAction = $headerSearchAction;
    }

    /**
     * @throws BindingResolutionException
     */
    public function view()
    {
        $this->set('form', $this->app->make('helper/form'));
        $this->set('token', $this->app->make('token'));

        if (isset($this->headerSearchAction)) {
            $this->set('headerSearchAction', $this->headerSearchAction);
        } else {
            $this->set('headerSearchAction', $this->app->make('url')->to('/dashboard/simple_newsletter/subscribers'));
        }

        if (isset($this->query)) {
            $this->set('query', $this->app->make(JsonSerializer::class)->serialize($this->query, 'json'));
        }
    }

}
