<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Form\Service\Validator;

use Error;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use WebServCo\Form\Service\FormField;
use WebServCo\Form\Service\Validator\AbstractValidator;
use WebServCo\Form\Service\Validator\MaximumLengthValidator;

#[CoversClass(MaximumLengthValidator::class)]
#[UsesClass(AbstractValidator::class)]
#[UsesClass(FormField::class)]
final class MaximumLengthValidatorTest extends TestCase
{
    #[DataProvider('valueProvider')]
    public function testValidate(?string $value, bool $isRequired, bool $expected): void
    {
        $validator = new MaximumLengthValidator(new Error('Too long.'), 3);
        $formField = new FormField([], 'id', $isRequired, 'id', '', '', [], $value);

        self::assertSame($expected, $validator->validate($formField));
    }

    /**
     * @return iterable<string,array{0:?string,1:bool,2:bool}> Value, required, expected result.
     */
    public static function valueProvider(): iterable
    {
        yield 'shorter' => ['ab', true, true];

        yield 'exact, counted in characters' => ['ábc', true, true];

        yield 'longer' => ['abcd', true, false];

        yield 'empty, not required' => ['', false, true];

        // Emptiness is the required validator's error: an empty field gets one error, not two.
        yield 'empty, required' => ['', true, true];

        yield 'missing, required' => [null, true, true];
    }
}
