<?php

declare(strict_types=1);

namespace WebServCo\Form\Contract;

interface FormValidatorInterface
{
    public function getErrorMessage(): string;

    public function validate(FormFieldInterface $formField): bool;
}
