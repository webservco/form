<?php

declare(strict_types=1);

namespace WebServCo\Form\Service\Validator;

use Throwable;
use WebServCo\Form\Contract\FormValidatorInterface;

abstract class AbstractValidator implements FormValidatorInterface
{
    public function __construct(private Throwable $error)
    {
    }

    public function getError(): Throwable
    {
        return $this->error;
    }
}
