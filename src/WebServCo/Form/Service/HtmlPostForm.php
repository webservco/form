<?php

declare(strict_types=1);

namespace WebServCo\Form\Service;

use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Http\Contract\Message\Request\Method\RequestMethodServiceInterface;

use function array_key_exists;
use function is_array;

final class HtmlPostForm extends AbstractForm implements FormInterface
{
    public function handleRequest(ServerRequestInterface $request): bool
    {
        // Check request method.
        if ($request->getMethod() !== RequestMethodServiceInterface::METHOD_POST) {
            $this->addErrorMessage('Request method doesn\'t match');

            return false;
        }

        // Request method matches, set flag.
        $this->isSent = true;

        // Get post data. This should be an array in these conditions.
        $parsedBody = $request->getParsedBody();
        if (!is_array($parsedBody)) {
            $this->addErrorMessage('Data is not an array.');

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
            $id = $formField->getId();
            if (!array_key_exists($id, $parsedBody)) {
                continue;
            }

            $formField->setValue((string) $parsedBody[$id]);
        }

        // Filter and validate each field.
        return $this->processForm();
    }
}
