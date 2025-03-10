<?php

declare(strict_types=1);

namespace WebServCo\Form\Service;

use Error;
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormInterface;

use function array_key_exists;
use function is_array;
use function is_scalar;
use function sprintf;
use function strval;

final class HtmlPostForm extends AbstractForm implements FormInterface
{
    public function handleRequest(ServerRequestInterface $request): bool
    {
        // Check request method.
        if ($request->getMethod() !== RequestMethodInterface::METHOD_POST) {
            /**
             * "405 Method Not Allowed"
             * "A request method is not supported for the requested resource;
             * for example, a GET request on a form that requires data to be presented via POST,
             * or a PUT request on a read-only resource."
             */
            $this->addError(new Error('Request method does not match', StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED));

            return false;
        }

        // Request method matches, set flag.
        $this->setSent();

        // Get post data. This should be an array in these conditions.
        $parsedBody = $request->getParsedBody();
        if (!is_array($parsedBody)) {
            $this->addError(new Error('Data is not an array.', StatusCodeInterface::STATUS_BAD_REQUEST));

            return false;
        }

        /**
         * Start from local fields and iterate,
         * because id is stored in the actual formField (string key),
         * it is not the array key, which is an integer.
         *
         * This also avoids having to check fields existence locally,
         * it simply only process stuff that we need.
         */
        foreach ($this->getFields() as $formField) {
            $this->handleFormField($formField, $parsedBody);
        }

        // Filter and validate each field.
        return $this->processForm();
    }

    /**
     * @phpcs:ignore SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @param array<mixed> $parsedBody
     */
    private function handleFormField(FormFieldInterface $formField, array $parsedBody): bool
    {
        $id = $formField->getId();
        if (!array_key_exists($id, $parsedBody)) {
            return false;
        }

        $value = $parsedBody[$id];
        if (!is_scalar($value)) {
            $this->addError(new Error(
                sprintf('Data is not a scalar for field "%s".', $id),
                StatusCodeInterface::STATUS_BAD_REQUEST,
            ),);

            return false;
        }

        return $formField->setValue(strval($value));
    }
}
