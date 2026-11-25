<?php

declare(strict_types=1);

namespace WebServCo\Form\Service\Validator;

use Override;
use Throwable;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormValidatorInterface;

use function in_array;

/**
 * The value is one of a list, eg. the options of a select. Compared as it is: same text, same case.
 */
final class AllowedValuesValidator extends AbstractValidator implements FormValidatorInterface
{
    /**
     * @param array<int,string> $allowedValues
     */
    public function __construct(Throwable $error, private array $allowedValues)
    {
        parent::__construct($error);
    }

    #[Override]
    public function validate(FormFieldInterface $formField): bool
    {
        $value = $formField->getValue();

        if ($value === null || $value === '') {
            /**
             * Value is not set or empty: no value to check.
             * A required field is checked by `RequiredValidator`, so that an empty field gets one error, not two.
             */
            return true;
        }

        return in_array($value, $this->allowedValues, true);
    }
}
