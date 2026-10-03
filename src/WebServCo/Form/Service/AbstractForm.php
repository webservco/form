<?php

declare(strict_types=1);

namespace WebServCo\Form\Service;

use Fig\Http\Message\StatusCodeInterface;
use OutOfBoundsException;
use Override;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use UnexpectedValueException;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormInterface;

abstract class AbstractForm implements FormInterface
{
    /**
     * @var array<int,\Throwable>
     */
    private array $errors = [];

    private bool $isSent = false;

    // Innocent until proven guilty.
    private bool $isValid = true;

    #[Override]
    abstract public function handleRequest(ServerRequestInterface $request): bool;

    /**
     * @param array<int,\WebServCo\Form\Contract\FormFieldInterface> $fields
     * @param array<int,\WebServCo\Form\Contract\FormFilterInterface> $filters
     * @param array<int,\WebServCo\Form\Contract\FormValidatorInterface> $validators
     */
    public function __construct(private array $fields, private array $filters, private array $validators)
    {
    }

    #[Override]
    public function addError(Throwable $error): bool
    {
        $this->setNotValid();

        $this->errors[] = $error;

        return true;
    }

    #[Override]
    public function addFormFieldErrorMessage(Throwable $error, FormFieldInterface $formField): bool
    {
        $this->setNotValid();

        return $formField->addError($error);
    }

    /**
     * @return array<int,\Throwable>
     */
    #[Override]
    public function getErrors(): array
    {
        return $this->errors;
    }

    #[Override]
    public function getField(string $id): FormFieldInterface
    {
        foreach ($this->fields as $formField) {
            if ($formField->getId() === $id) {
                return $formField;
            }
        }

        throw new OutOfBoundsException('Requested field not found.');
    }

    /**
     * @return array<int,\WebServCo\Form\Contract\FormFieldInterface>
     */
    #[Override]
    public function getFields(): array
    {
        return $this->fields;
    }

    #[Override]
    public function getResponseStatusCode(): int
    {
        if ($this->isSent()) {
            if (!$this->isValid()) {
                return StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY;
            }

            // Form is sent and valid; should not arrive here.
            throw new UnexpectedValueException('Unhandled situation.');
        }

        // Form is not sent.
        return StatusCodeInterface::STATUS_OK;
    }

    #[Override]
    public function isSent(): bool
    {
        return $this->isSent;
    }

    #[Override]
    public function isValid(): bool
    {
        return $this->isValid;
    }

    #[Override]
    public function setNotValid(): bool
    {
        $this->isValid = false;

        return true;
    }

    #[Override]
    public function setSent(): bool
    {
        $this->isSent = true;

        return true;
    }

    protected function processForm(): bool
    {
        foreach ($this->fields as $formField) {
            // Filter field.
            $this->filterField($formField);

            // Validate field.
            $this->validateField($formField);
        }

        return true;
    }

    private function filterField(FormFieldInterface $formField): bool
    {
        // Apply form level filters.
        foreach ($this->filters as $filter) {
            $formField->setValue($filter->filter($formField->getValue()));
        }

        // Apply field level filters.
        foreach ($formField->getFilters() as $filter) {
            $formField->setValue($filter->filter($formField->getValue()));
        }

        return true;
    }

    /**
     * Perform individual field validation.
     */
    private function validateField(FormFieldInterface $formField): bool
    {
        $this->validateFieldGeneral($formField);

        $this->validateFieldIndividual($formField);

        return true;
    }

    /**
     * Validate FormField using general validators.
     */
    private function validateFieldGeneral(FormFieldInterface $formField): bool
    {
        foreach ($this->validators as $validator) {
            if ($validator->validate($formField)) {
                continue;
            }
            $this->addFormFieldErrorMessage($validator->getError(), $formField);
        }

        return true;
    }

    /**
     * Validate FormField using individual validators.
     */
    private function validateFieldIndividual(FormFieldInterface $formField): bool
    {
        foreach ($formField->getValidators() as $validator) {
            if ($validator->validate($formField)) {
                continue;
            }
            $this->addFormFieldErrorMessage($validator->getError(), $formField);
        }

        return true;
    }
}
