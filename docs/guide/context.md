# Sharp Context

Sharp provide a way to grab some request context values in the application code.

## Ask for context

The class handling the context is `Code16\Sharp\Http\Context\CurrentSharpRequest`, which is a singleton. So at any point in the request, you can get it via:

```php
app(Code16\Sharp\Http\Context\CurrentSharpRequest::class);
```

or with the global helper:

```php
currentSharpRequest();
```

Here's a quick example:

```php
class MyForm extends SharpForm
{
    function buildFormFields()
    {
        $this->addField(
            SharpFormTextField::make("name")
                ->setReadOnly(currentSharpRequest()->isUpdate())
        );
        [...]
    }
    [...]
}
```

The context is often useful in a Form situation to display or hide fields depending on the instance status (creation, update), or in a Validator to add an id exception in an unique rule.

## Methods

### `entityKey(): string`

Grab the current entity key.

### `isEntityList(): bool`
### `isShow(): bool`
### `isForm(): bool`

Find out the current page type.

### `isUpdate(): bool`
### `isCreation(): bool`

In Form case, check the current status.

### `instanceId(): string`

In Form and Show cases, grab the instance id.

### `breadcrumb(): Collection`

Returns a Collection of `Code16\Sharp\Http\Context\Util\BreadcrumbItem`s

### `getCurrentBreadcrumbItem(): BreadcrumbItem`

Get the current breadcrumb item.

### `getPreviousShowFromBreadcrumbItems(): ?BreadcrumbItem`

Get (if existing) the closest Show in the breadcrumb.

### `globalFilterFor($filterName)`

Get the value of a Global Filter: see the [Global Filter documentation](filters.md) to know more about this feature.
