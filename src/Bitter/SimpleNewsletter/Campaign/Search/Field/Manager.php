<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpMissingFieldTypeInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Campaign\Search\Field;

use Bitter\SimpleNewsletter\Campaign\Search\Field\Field\CreatedAtField;
use Bitter\SimpleNewsletter\Campaign\Search\Field\Field\MailingListField;
use Bitter\SimpleNewsletter\Campaign\Search\Field\Field\NameField;
use Bitter\SimpleNewsletter\Campaign\Search\Field\Field\SentAtField;
use Bitter\SimpleNewsletter\Campaign\Search\Field\Field\StateField;
use Bitter\SimpleNewsletter\Campaign\Search\Field\Field\SubjectField;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\Manager as FieldManager;

class Manager extends FieldManager
{
    public function __construct()
    {
        $this->addGroup(t('Core Properties'), [
            new KeywordsField(),
            new NameField(),
            new SubjectField(),
            new MailingListField(),
            new CreatedAtField(),
            new SentAtField(),
            new StateField()
        ]);
    }
}
