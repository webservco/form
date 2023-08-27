<?php

declare(strict_types=1);

namespace WebServCo\Form\Service\Filter;

use UnexpectedValueException;
use WebServCo\Form\Contract\FormFilterInterface;

use function is_string;
use function preg_replace;

final class NumberFilter implements FormFilterInterface
{
    /**
     * Remove anything that is not a digit.
     */
    public function filter(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $result = preg_replace('/[^0-9]/', '', $value);
        if (!is_string($result)) {
            throw new UnexpectedValueException('Result is not a string');
        }

        return $result;
    }
}
