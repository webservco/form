<?php

declare(strict_types=1);

namespace WebServCo\Form\Service\Validator;

use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormValidatorInterface;

use function mb_strlen;

final class MinimumLengthValidator implements FormValidatorInterface
{
    public function __construct(private string $errorMessage, private int $minimumLength)
    {
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

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

        return mb_strlen($value) >= $this->minimumLength;
    }
}
