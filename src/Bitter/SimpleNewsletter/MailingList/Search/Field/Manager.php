<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpMissingFieldTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\MailingList\Search\Field;

use Bitter\SimpleNewsletter\MailingList\Search\Field\Field\NameField;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\Manager as FieldManager;

class Manager extends FieldManager
{
    public function __construct()
    {
        $this->addGroup(t('Core Properties'), [
            new KeywordsField(),
            new NameField()
        ]);
    }
}
