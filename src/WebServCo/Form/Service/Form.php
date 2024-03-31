<?php

declare(strict_types=1);

namespace WebServCo\Form\Service;

use Fig\Http\Message\StatusCodeInterface;
use OutOfBoundsException;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Http\Contract\Message\Request\Method\RequestMethodServiceInterface;

use function array_key_exists;
use function is_array;

final class Form implements FormInterface
{
    private bool $isSent = false;

    // Innocent until proven guilty.
    private bool $isValid = true;

    /**
     * @param array<int,\WebServCo\Form\Contract\FormFieldInterface> $fields
     * @param array<int,\WebServCo\Form\Contract\FormFilterInterface> $filters
     * @param array<int,\WebServCo\Form\Contract\FormValidatorInterface> $validators
     */
    public function __construct(private array $fields, private array $filters, private array $validators)
    {
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

    public function handleRequest(ServerRequestInterface $request): bool
    {
        // Check POST.
        if ($request->getMethod() !== RequestMethodServiceInterface::METHOD_POST) {
            return false;
        }

        // Method is POST, set flag.
        $this->isSent = true;

        // Get post data. This should be an array in these conditions.
        $parsedBody = $request->getParsedBody();
        if (!is_array($parsedBody)) {
            throw new UnexpectedValueException('Data is not an array.');
        }

        foreach ($this->fields as $formField) {
            if (array_key_exists($formField->getId(), $parsedBody)) {
                $formField->setValue((string) $parsedBody[$formField->getId()]);
                $this->filter($formField);
            }

            // Validate field.
            $this->validate($formField);
        }

        return true;
    }

    public function isSent(): bool
    {
        return $this->isSent;
    }

    public function isValid(): bool
    {
        return $this->isValid;
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
