<?php

declare(strict_types=1);

namespace WebServCo\Form\Service\Validator;

use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormValidatorInterface;

use function filter_var;

use const FILTER_VALIDATE_EMAIL;

final class EmailAddressValidator extends AbstractValidator implements FormValidatorInterface
{
    public function validate(FormFieldInterface $formField): bool
    {
        $value = $formField->getValue();

        if ($value === null || $value === '') {
            /**
             * Value is not set or empty.
             * Return true (valid) if field is not required, and false (invalid) is field is required.
             */
            return !$formField->isRequired();
        }

        $result = filter_var($value, FILTER_VALIDATE_EMAIL);

        return $result !== false;
    }
}
