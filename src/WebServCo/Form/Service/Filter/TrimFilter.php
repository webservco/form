<?php

declare(strict_types=1);

namespace WebServCo\Form\Service\Filter;

use Override;
use WebServCo\Form\Contract\FormFilterInterface;

use function trim;

final class TrimFilter implements FormFilterInterface
{
    #[Override]
    public function filter(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim($value);
    }
}
