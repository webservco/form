<?php

declare(strict_types=1);

namespace WebServCo\Form\Service\Filter;

use Override;
use WebServCo\Form\Contract\FormFilterInterface;

use function strip_tags;

final class StripTagsFilter implements FormFilterInterface
{
    #[Override]
    public function filter(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return strip_tags($value);
    }
}
