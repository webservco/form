<?php

declare(strict_types=1);

namespace WebServCo\Form\Contract;

interface FormFieldInterface
{
    public function addErrorMessage(string $errorMessage): bool;

    /**
     * @return array<int,string>
     */
    public function getErrorMessages(): array;

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
