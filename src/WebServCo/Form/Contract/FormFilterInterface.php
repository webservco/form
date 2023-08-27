<?php

declare(strict_types=1);

namespace WebServCo\Form\Contract;

interface FormFilterInterface
{
    public function filter(?string $value): ?string;
}
