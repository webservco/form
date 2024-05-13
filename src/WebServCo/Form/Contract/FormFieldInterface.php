<?php

declare(strict_types=1);

namespace WebServCo\Form\Contract;

use Throwable;

interface FormFieldInterface
{
    public function addError(Throwable $error): bool;

    /**
     * @return array<int,\Throwable>
     */
    public function getErrors(): array;

    /**
     * @return array<int,\WebServCo\Form\Contract\FormFilterInterface>
     */
    public function getFilters(): array;

    public function getId(): string;

    public function getName(): string;

    public function getPlaceholder(): string;

    public function getTitle(): string;

    /**
     * @return array<int,\WebServCo\Form\Contract\FormValidatorInterface>
     */
    public function getValidators(): array;

    public function getValue(): ?string;

    public function isRequired(): bool;

    public function setValue(?string $value): bool;
}
