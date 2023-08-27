<?php

declare(strict_types=1);

namespace WebServCo\Form\Service;

use WebServCo\Form\Contract\FormFieldInterface;

final class FormField implements FormFieldInterface
{
    /**
     * @var array<int,string>
     */
    private array $errorMessages = [];

    /**
     * @param array<int,\WebServCo\Form\Contract\FormFilterInterface> $filters
     * @param array<int,\WebServCo\Form\Contract\FormValidatorInterface> $validators
     */
    public function __construct(
        private array $filters,
        private string $id,
        private bool $isRequired,
        private string $name,
        private string $placeholder,
        private string $title,
        private array $validators,
        // Set default field value
        private ?string $value,
    ) {
    }

    public function addErrorMessage(string $errorMessage): bool
    {
        $this->errorMessages[] = $errorMessage;

        return true;
    }

    /**
     * @return array<int,string>
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @return array<int,\WebServCo\Form\Contract\FormFilterInterface>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return array<int,\WebServCo\Form\Contract\FormValidatorInterface>
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function setValue(?string $value): bool
    {
        $this->value = $value;

        return true;
    }
}
