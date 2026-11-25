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
use WebServCo\Form\Service\Validator\AllowedValuesValidator;

#[CoversClass(AllowedValuesValidator::class)]
#[UsesClass(AbstractValidator::class)]
#[UsesClass(FormField::class)]
final class AllowedValuesValidatorTest extends TestCase
{
    #[DataProvider('valueProvider')]
    public function testValidate(?string $value, bool $isRequired, bool $expected): void
    {
        $validator = new AllowedValuesValidator(new Error('Not in the list.'), ['Europe/Rome', '0', '10']);
        $formField = new FormField([], 'id', $isRequired, 'id', '', '', [], $value);

        self::assertSame($expected, $validator->validate($formField));
    }

    public function testAnEmptyListAllowsNoValue(): void
    {
        $validator = new AllowedValuesValidator(new Error('Not in the list.'), []);

        self::assertFalse($validator->validate(new FormField([], 'id', true, 'id', '', '', [], 'abc')));
    }

    /**
     * @return iterable<string,array{0:?string,1:bool,2:bool}> Value, required, expected result.
     */
    public static function valueProvider(): iterable
    {
        yield 'in the list' => ['Europe/Rome', true, true];

        yield 'in the list, not required' => ['Europe/Rome', false, true];

        yield 'in the list, zero' => ['0', true, true];

        yield 'not in the list' => ['Europe/Paris', true, false];

        yield 'not in the list, not required' => ['Europe/Paris', false, false];

        yield 'another case' => ['europe/rome', true, false];

        // Equal as numbers, not as text.
        yield 'another number format' => ['1e1', true, false];

        yield 'empty, not required' => ['', false, true];

        // Emptiness is the required validator's error: an empty field gets one error, not two.
        yield 'empty, required' => ['', true, true];

        yield 'missing, required' => [null, true, true];
    }
}
