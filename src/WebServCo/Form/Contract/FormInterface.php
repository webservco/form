<?php

declare(strict_types=1);

namespace WebServCo\Form\Contract;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

interface FormInterface
{
    public function addError(Throwable $error): bool;

    /**
     * Convenience method to make sure form is also invalidated.
     */
    public function addFormFieldErrorMessage(Throwable $error, FormFieldInterface $formField): bool;

    /**
     * @return array<int,\Throwable>
     */
    public function getErrors(): array;

    public function getField(string $id): FormFieldInterface;

    /**
     * @return array<int,\WebServCo\Form\Contract\FormFieldInterface>
     */
    public function getFields(): array;

    /**
     * Get the status code that should be used in the Response, based on the form status.
     */
    public function getResponseStatusCode(): int;

    public function handleRequest(ServerRequestInterface $request): bool;

    public function isSent(): bool;

    public function isValid(): bool;

    public function setNotValid(): bool;

    public function setSent(): bool;
}
