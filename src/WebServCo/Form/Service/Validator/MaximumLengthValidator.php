<?php

declare(strict_types=1);

namespace WebServCo\Form\Service\Validator;

use Override;
use Throwable;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormValidatorInterface;

use function mb_strlen;

final class MaximumLengthValidator extends AbstractValidator implements FormValidatorInterface
{
    public function __construct(Throwable $error, private int $maximumLength)
    {
        parent::__construct($error);
    }

    #[Override]
    public function validate(FormFieldInterface $formField): bool
    {
        $value = $formField->getValue();

        if ($value === null || $value === '') {
            /**
             * Value is not set or empty: no length to check.
             * A required field is checked by `RequiredValidator`, so that an empty field gets one error, not two.
             */
            return true;
        }

        return mb_strlen($value) <= $this->maximumLength;
    }
}
