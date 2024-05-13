<?php

declare(strict_types=1);

namespace WebServCo\Form\Service\Validator;

use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormValidatorInterface;

final class RequiredValidator extends AbstractValidator implements FormValidatorInterface
{
    public function validate(FormFieldInterface $formField): bool
    {
        if (!$formField->isRequired()) {
            // Field is not required, nothing to do. Validation passes.
            return true;
        }

        // Field is required.

        $value = $formField->getValue();

        // Check if empty.
        return $value !== null && $value !== '';
    }
}
