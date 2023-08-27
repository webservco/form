<?php

declare(strict_types=1);

namespace WebServCo\Form\Service\Filter;

use WebServCo\Form\Contract\FormFilterInterface;

use function trim;

final class TrimFilter implements FormFilterInterface
{
    public function filter(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim($value);
    }
}
