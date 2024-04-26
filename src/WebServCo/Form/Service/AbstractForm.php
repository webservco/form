<?php

declare(strict_types=1);

namespace WebServCo\Form\Service;

use Fig\Http\Message\StatusCodeInterface;
use OutOfBoundsException;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormInterface;

abstract class AbstractForm implements FormInterface
{
    /**
     * @var array<int,string>
     */
    private array $errorMessages = [];

    private bool $isSent = false;

    // Innocent until proven guilty.
    private bool $isValid = true;

    abstract public function handleRequest(ServerRequestInterface $request): bool;

    /**
     * @param array<int,\WebServCo\Form\Contract\FormFieldInterface> $fields
     * @param array<int,\WebServCo\Form\Contract\FormFilterInterface> $filters
     * @param array<int,\WebServCo\Form\Contract\FormValidatorInterface> $validators
     */
    public function __construct(private array $fields, private array $filters, private array $validators)
    {
    }

    public function addErrorMessage(string $errorMessage): bool
    {
        $this->setNotValid();

        $this->errorMessages[] = $errorMessage;

        return true;
    }

    public function addFormFieldErrorMessage(string $errorMessage, FormFieldInterface $formField): bool
    {
        $this->setNotValid();

        return $formField->addErrorMessage($errorMessage);
    }

    /**
     * @return array<int,string>
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

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
    public function getFields(): array
    {
        return $this->fields;
    }

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

    public function isSent(): bool
    {
        return $this->isSent;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function setNotValid(): bool
    {
        $this->isValid = false;

        return true;
    }

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
            $this->addFormFieldErrorMessage($validator->getErrorMessage(), $formField);
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
            $this->addFormFieldErrorMessage($validator->getErrorMessage(), $formField);
        }

        return true;
    }
}
