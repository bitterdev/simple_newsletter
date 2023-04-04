<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Campaign\Search\Result;

use Concrete\Core\Search\Result\Result as SearchResult;

class Result extends SearchResult
{
    public function getItemDetails($item)
    {
        return new Item($this, $this->listColumns, $item);
    }

    public function getColumnDetails($column)
    {
        return new Column($this, $column);
    }
}
