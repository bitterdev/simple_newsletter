<?php /** @noinspection DuplicatedCode */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Concrete\Package\SimpleNewsletter\Controller\Element\Campaigns\Search;

use Bitter\SimpleNewsletter\Campaign\Search\SearchProvider;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Utility\Service\Url;
use Concrete\Core\Validation\CSRF\Token;
use Illuminate\Contracts\Container\BindingResolutionException;

class Menu extends ElementController
{
    protected $query;
    protected $searchProvider;

    public function __construct(SearchProvider $searchProvider)
    {
        parent::__construct();
        $this->searchProvider = $searchProvider;
    }

    public function getElement(): string
    {
        return 'campaigns/search/menu';
    }

    public function setQuery(Query $query): void
    {
        $this->query = $query;
    }

    /**
     * @throws BindingResolutionException
     */
    public function view()
    {
        $itemsPerPage = (isset($this->query)) ? $this->query->getItemsPerPage() : $this->searchProvider->getItemsPerPage();
        $this->set('itemsPerPage', $itemsPerPage);
        $this->set('itemsPerPageOptions', $this->searchProvider->getItemsPerPageOptions());
        $this->set('form', $this->app->make(Form::class));
        $this->set('token', $this->app->make(Token::class));
        $this->set('urlHelper', $this->app->make(Url::class));
    }

}
