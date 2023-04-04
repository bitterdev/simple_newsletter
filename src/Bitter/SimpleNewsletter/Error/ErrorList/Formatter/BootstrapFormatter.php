<?php

/**
* @project:   Simple Newsletter
*
* @author     Fabian Bitter (fabian@bitter.de)
* @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
* @version    X.X.X
*/

namespace Bitter\SimpleNewsletter\Error\ErrorList\Formatter;

use Concrete\Core\Error\ErrorList\Error\HtmlAwareErrorInterface;
use Concrete\Core\Error\ErrorList\Formatter\AbstractFormatter;

class BootstrapFormatter extends AbstractFormatter
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Error\ErrorList\Formatter\FormatterInterface::render()
     */
    public function render()
    {
        return $this->getString();
    }

    /**
     * Build an HTML-formatted string describing the errors.
     *
     * @return string
     */
    public function getString()
    {
        $html = '';
        if ($this->error->has()) {
            foreach ($this->error->getList() as $error) {
                $html .= '<div class="alert alert-danger">';
                if ($error instanceof HtmlAwareErrorInterface && $error->messageContainsHtml()) {
                    $html .= (string) $error;
                } else {
                    $html .= nl2br(h((string) $error));
                }
                $html .= '</div>';
            }
        }

        return $html;
    }
}
