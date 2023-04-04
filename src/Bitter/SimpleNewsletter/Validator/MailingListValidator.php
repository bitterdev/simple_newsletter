<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleNewsletter\Validator;

use Bitter\SimpleNewsletter\Entity\MailingList as MailingListEntity;
use Bitter\SimpleNewsletter\Service\MailingList;
use Concrete\Core\Validator\AbstractTranslatableValidator;
use ArrayAccess;

class MailingListValidator extends AbstractTranslatableValidator
{
    protected $mailingListService;

    public function __construct(
        MailingList $mailingListService
    )
    {
        $this->mailingListService = $mailingListService;
    }

    public function isValid($mixed, ArrayAccess $error = null)
    {
        if (!is_array($mixed)) {
            $mixed = [$mixed];
        }

        foreach($mixed as $id) {
            if (!$this->mailingListService->getMailingListById($id) instanceof MailingListEntity) {
                $error[] = t('The given mailing list does not exists.');
                return false;
            }
        }

        return true;
    }
}