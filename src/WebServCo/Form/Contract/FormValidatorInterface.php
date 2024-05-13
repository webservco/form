<?php

declare(strict_types=1);

namespace WebServCo\Form\Contract;

use Throwable;

interface FormValidatorInterface
{
    public function getError(): Throwable;

    public function validate(FormFieldInterface $formField): bool;
}
