<?php

declare(strict_types=1);

namespace WebServCo\Form\Service;

use Override;
use Throwable;
use WebServCo\Form\Contract\FormFieldInterface;

final class FormField implements FormFieldInterface
{
    /**
     * @var array<int,\Throwable>
     */
    private array $errors = [];

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

    #[Override]
    public function addError(Throwable $error): bool
    {
        $this->errors[] = $error;

        return true;
    }

    /**
     * @return array<int,\Throwable>
     */
    #[Override]
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array<int,\WebServCo\Form\Contract\FormFilterInterface>
     */
    #[Override]
    public function getFilters(): array
    {
        return $this->filters;
    }

    #[Override]
    public function getId(): string
    {
        return $this->id;
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    #[Override]
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return array<int,\WebServCo\Form\Contract\FormValidatorInterface>
     */
    #[Override]
    public function getValidators(): array
    {
        return $this->validators;
    }

    #[Override]
    public function getValue(): ?string
    {
        return $this->value;
    }

    #[Override]
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    #[Override]
    public function setValue(?string $value): bool
    {
        $this->value = $value;

        return true;
    }
}
