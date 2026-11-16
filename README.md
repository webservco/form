# webservco/form

A PHP component/library. Please see [webservco/sandbox](https://webservco.github.io/sandbox/) for an example project using these types of components.

HTML forms with filters and validators, and error messages for both the form and each field.

- Related: `webservco/jsonapi-application` (its request handler is a form too), `webservco/view` (templates that display the form)

---

## Installation

```shell
composer require webservco/form
```

---

## Usage

Build the form in a factory. Each controller then gets its own instance.

```php
use Fig\Http\Message\StatusCodeInterface;
use WebServCo\Form\Service\Filter\StripTagsFilter;
use WebServCo\Form\Service\Filter\TrimFilter;
use WebServCo\Form\Service\FormField;
use WebServCo\Form\Service\HtmlPostForm;
use WebServCo\Form\Service\Validator\MaximumLengthValidator;
use WebServCo\Form\Service\Validator\RequiredValidator;

// \WebServCo\Form\Contract\FormInterface
$form = new HtmlPostForm(
    [
        new FormField(
            // Field filters.
            [],
            // id: the POST key.
            'name',
            // Required.
            true,
            // name, placeholder, title
            'name',
            'Item name',
            'Name',
            // Field validators.
            [new MaximumLengthValidator(new Error('Maximum length: 90 chars.', StatusCodeInterface::STATUS_BAD_REQUEST), 90)],
            // Initial value.
            null,
        ),
    ],
    // Filters for every field.
    [new TrimFilter(), new StripTagsFilter()],
    // Validators for every field.
    [new RequiredValidator(new Error('Field is required.', StatusCodeInterface::STATUS_BAD_REQUEST))],
);
```

In the controller:

```php
$form->handleRequest($request);

if ($form->isSent() && $form->isValid()) {
    $name = $form->getField('name')->getValue();
    // Save, then redirect.
}

// Show the form again: 200 on first display, 422 when it has errors.
return $this->createResponse($request, $viewContainer, $form->getResponseStatusCode());
```

In the template, `$form->getErrors()` has the form errors and `$form->getField('name')->getErrors()` has the errors for one field.

---

## Contracts

| Interface | Implementation | Purpose |
| --- | --- | --- |
| `Contract\FormInterface` | `Service\HtmlPostForm` (extends `Service\AbstractForm`) | Reads POST data, filters and validates every field. |
| `Contract\FormFieldInterface` | `Service\FormField` | One field: value, settings, filters, validators and errors. |
| `Contract\FormFilterInterface` | `Service\Filter\TrimFilter`, `StripTagsFilter`, `NumberFilter` | Changes the value before validation. `NumberFilter` keeps only digits. |
| `Contract\FormValidatorInterface` | `Service\Validator\RequiredValidator`, `MinimumLengthValidator`, `MaximumLengthValidator`, `EmailAddressValidator` | Checks the value and gives back its error when the check fails. |
| `Contract\FormFactoryInterface` | your factory | Creates the form. |

Namespaces are relative to `WebServCo\Form\`.

---

## Notes

Form-level filters run before the field's own filters, and form-level validators run before the field's own validators. Every field gets all of them. The length validators skip empty values, required or not: an empty required field fails `RequiredValidator` alone, so use it on every form that has required fields.

A request that isn't POST gets a form error with code 405, and the form isn't marked as sent. An array in the POST data where a field expects a single value is rejected with a form error.

`StripTagsFilter` cleans input, but it doesn't make output safe. Escape values in templates as well.

---

## Development

```shell
# Start project.
ddev start
# Install dependencies.
ddev c u
# Run all validations.
ddev c c
# Run unit tests.
ddev c test:dox
```

---

## License

[MIT](LICENSE.md)
