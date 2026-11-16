<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Form\Service;

use Error;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Form\Service\AbstractForm;
use WebServCo\Form\Service\Filter\TrimFilter;
use WebServCo\Form\Service\FormField;
use WebServCo\Form\Service\HtmlPostForm;
use WebServCo\Form\Service\Validator\AbstractValidator;
use WebServCo\Form\Service\Validator\MaximumLengthValidator;
use WebServCo\Form\Service\Validator\MinimumLengthValidator;
use WebServCo\Form\Service\Validator\RequiredValidator;

use function array_map;

#[CoversClass(AbstractForm::class)]
#[CoversClass(HtmlPostForm::class)]
#[UsesClass(AbstractValidator::class)]
#[UsesClass(FormField::class)]
#[UsesClass(MaximumLengthValidator::class)]
#[UsesClass(MinimumLengthValidator::class)]
#[UsesClass(RequiredValidator::class)]
#[UsesClass(TrimFilter::class)]
final class HtmlPostFormTest extends TestCase
{
    public function testAFormIsNotSentByAGetRequest(): void
    {
        $form = $this->createForm();

        self::assertFalse($form->handleRequest($this->createRequest('GET', [])));
        self::assertFalse($form->isSent());
        self::assertSame(200, $form->getResponseStatusCode());
        self::assertSame([405], $this->getErrorCodes($form->getErrors()));
    }

    public function testAValidValueIsFiltered(): void
    {
        $form = $this->createForm();
        $form->handleRequest($this->createRequest('POST', ['name' => '  abc  ']));

        self::assertTrue($form->isSent());
        self::assertTrue($form->isValid());
        self::assertSame('abc', $form->getField('name')->getValue());
    }

    public function testAnEmptyRequiredFieldGetsOneError(): void
    {
        $form = $this->createForm();
        $form->handleRequest($this->createRequest('POST', ['name' => '   ']));

        self::assertFalse($form->isValid());
        self::assertSame(422, $form->getResponseStatusCode());
        self::assertSame(['Required.'], $this->getFieldErrorMessages($form));
    }

    public function testAMissingRequiredFieldGetsOneError(): void
    {
        $form = $this->createForm();
        $form->handleRequest($this->createRequest('POST', []));

        self::assertSame(['Required.'], $this->getFieldErrorMessages($form));
    }

    public function testATooLongValueGetsTheLengthError(): void
    {
        $form = $this->createForm();
        $form->handleRequest($this->createRequest('POST', ['name' => 'abcdef']));

        self::assertSame(['Too long.'], $this->getFieldErrorMessages($form));
    }

    public function testAnArrayValueIsAFormError(): void
    {
        $form = $this->createForm();
        $form->handleRequest($this->createRequest('POST', ['name' => ['abc']]));

        self::assertFalse($form->isValid());
        self::assertSame([400], $this->getErrorCodes($form->getErrors()));
    }

    private function createForm(): FormInterface
    {
        return new HtmlPostForm(
            [
                new FormField(
                    [],
                    'name',
                    true,
                    'name',
                    '',
                    'Name',
                    [
                        new MinimumLengthValidator(new Error('Too short.', 400), 2),
                        new MaximumLengthValidator(new Error('Too long.', 400), 5),
                    ],
                    null,
                ),
            ],
            [new TrimFilter()],
            [new RequiredValidator(new Error('Required.', 400))],
        );
    }

    /**
     * @param array<string,array<string>|string> $parsedBody
     */
    private function createRequest(string $method, array $parsedBody): ServerRequestInterface
    {
        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn($method);
        $request->method('getParsedBody')->willReturn($parsedBody);

        return $request;
    }

    /**
     * @param array<int,\Throwable> $errors
     * @return array<int,int|string> `Throwable::getCode()` can be a string (`PDOException`).
     */
    private function getErrorCodes(array $errors): array
    {
        return array_map(static function (Throwable $error): int|string {
            return $error->getCode();
        }, $errors);
    }

    /**
     * @return array<int,string>
     */
    private function getFieldErrorMessages(FormInterface $form): array
    {
        return array_map(static function (Throwable $error): string {
            return $error->getMessage();
        }, $form->getField('name')->getErrors());
    }
}
