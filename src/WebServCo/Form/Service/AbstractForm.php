<?php

declare(strict_types=1);

namespace WebServCo\Form\Service;

use Fig\Http\Message\StatusCodeInterface;
use OutOfBoundsException;
use UnexpectedValueException;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormInterface;

abstract class AbstractForm implements FormInterface
{
    protected bool $isSent = false;

    // Innocent until proven guilty.
    protected bool $isValid = true;

    /**
     * @var array<int,string>
     */
    private array $errorMessages = [];

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

    public function getField(string $id): FormFieldInterface
    {
        foreach ($this->fields as $formField) {
            if ($formField->getId() === $id) {
                return $formField;
            }
        }

        throw new OutOfBoundsException('Requested field is not defined.');
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

    protected function fieldExists(string $id): bool
    {
        foreach ($this->fields as $formField) {
            if ($formField->getId() === $id) {
                return true;
            }
        }

        return false;
    }

    protected function processForm(): bool
    {
        foreach ($this->fields as $formField) {
            // Filter field.
            $this->filter($formField);

            // Validate field.
            $this->validate($formField);
        }

        return true;
    }

    private function filter(FormFieldInterface $formField): bool
    {
        foreach ($this->filters as $filter) {
            $formField->setValue($filter->filter($formField->getValue()));
        }

        foreach ($formField->getFilters() as $filter) {
            $formField->setValue($filter->filter($formField->getValue()));
        }

        return true;
    }

    /**
     * Perform individual field validation.
     */
    private function validate(FormFieldInterface $formField): bool
    {
        /** Check general validators. */
        $this->validateGeneral($formField);

        /** Check individual validators. */
        $this->validateIndividual($formField);

        return true;
    }

    private function validateGeneral(FormFieldInterface $formField): bool
    {
        foreach ($this->validators as $validator) {
            if ($validator->validate($formField)) {
                continue;
            }
            $this->isValid = false;
            $formField->addErrorMessage($validator->getErrorMessage());
        }

        return true;
    }

    private function validateIndividual(FormFieldInterface $formField): bool
    {
        foreach ($formField->getValidators() as $validator) {
            if ($validator->validate($formField)) {
                continue;
            }
            $this->isValid = false;
            $formField->addErrorMessage($validator->getErrorMessage());
        }

        return true;
    }
}
