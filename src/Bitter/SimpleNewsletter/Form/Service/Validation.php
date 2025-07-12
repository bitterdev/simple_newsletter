<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Form\Service;

use Bitter\SimpleNewsletter\Validator\MailingListValidator;
use Bitter\SimpleNewsletter\Validator\PageValidator;
use Concrete\Core\File\ValidationService;
use Concrete\Core\Form\Service\Validation as CoreValidation;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Validator\String\EmailValidator;
use stdClass;

class Validation extends CoreValidation
{
    const VALID_PAGE = 31;
    const VALID_MAILING_LIST = 32;

    public function addRequiredPage($field, $errorMsg = null)
    {
        $obj = new stdClass();
        $obj->message = ($errorMsg == null) ? t('Field "%s" is invalid', $field) : $errorMsg;
        $obj->field = $field;
        $obj->validate = self::VALID_PAGE;
        $this->fields[] = $obj;
    }

    public function addRequiredMailingList($field, $errorMsg = null)
    {
        $obj = new stdClass();
        $obj->message = ($errorMsg == null) ? t('Field "%s" is invalid', $field) : $errorMsg;
        $obj->field = $field;
        $obj->validate = self::VALID_MAILING_LIST;
        $this->fields[] = $obj;
    }

    public function test()
    {
        $app = Application::getFacadeApplication();
        /** @var MailingListValidator $mailingListValidator */
        $mailingListValidator = $app->make(MailingListValidator::class);
        /** @var PageValidator $pageValidator */
        $pageValidator = $app->make(PageValidator::class);
        /** @var Strings $stringValidator */
        $stringValidator = $app->make(Strings::class);
        /** @var Numbers $numberValidator */
        $numberValidator = $app->make(Numbers::class);
        /** @var ValidationService $fileValidator */
        $fileValidator = $app->make(ValidationService::class);
        /** @var Token $tokenValidator */
        $tokenValidator = $app->make(Token::class);
        /** @var EmailValidator $emailValidator */
        $emailValidator = $app->make(EmailValidator::class);

        foreach ($this->fields as $field) {
            $validate = $field->validate;
            $fieldName = isset($field->field) ? $field->field : null;
            $fieldValue = isset($this->data[$fieldName]) ? $this->data[$fieldName] : null;

            switch ($validate) {

                case self::VALID_PAGE:
                    if (!$pageValidator->isValid($fieldValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_MAILING_LIST:
                    if (!$mailingListValidator->isValid($fieldValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_NOT_EMPTY:
                    if (!$stringValidator->notempty($fieldValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_TOKEN:
                    if (!$tokenValidator->validate($field->value)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_INTEGER:
                    if ((!$numberValidator->integer($fieldValue)) && ($stringValidator->notempty($fieldValue))) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_INTEGER_REQUIRED:
                    if (!$numberValidator->integer($fieldValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_UPLOADED_IMAGE:
                    if ((!$fileValidator->image($this->files[$fieldName]['tmp_name'])) && ($this->files[$fieldName]['tmp_name'] != '')) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_UPLOADED_IMAGE_REQUIRED:
                    if (!$fileValidator->image($this->files[$fieldName]['tmp_name'])) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_UPLOADED_FILE:
                    if ((!$fileValidator->file($this->files[$fieldName]['tmp_name'])) && ($this->files[$fieldName]['tmp_name'] != '')) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_UPLOADED_FILE_REQUIRED:
                    if (!$fileValidator->file($this->files[$fieldName]['tmp_name'])) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_EMAIL:
                    if (!$emailValidator->isValid($fieldValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;
            }
        }

        $this->setErrorsFromInvalidFields();

        return count($this->fieldsInvalid) == 0;
    }

}