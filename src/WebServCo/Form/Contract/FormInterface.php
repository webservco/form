<?php

declare(strict_types=1);

namespace WebServCo\Form\Contract;

use Psr\Http\Message\ServerRequestInterface;

interface FormInterface
{
    public function getField(string $id): FormFieldInterface;

    public function handleRequest(ServerRequestInterface $request): bool;

    public function isSent(): bool;

    public function isValid(): bool;
}
