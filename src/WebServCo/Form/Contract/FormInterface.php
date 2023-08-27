<?php

declare(strict_types=1);

namespace WebServCo\Form\Contract;

use Psr\Http\Message\ServerRequestInterface;

interface FormInterface
{
    public function getField(string $id): FormFieldInterface;

    /**
     * Get the status code that should be used in the Response, based on the form status.
     */
    public function getResponseStatusCode(): int;

    public function handleRequest(ServerRequestInterface $request): bool;

    public function isSent(): bool;

    public function isValid(): bool;
}
