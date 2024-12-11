# Quick creation form

Sometimes you may want to allow the creation of a new instance directly from the list page, without having to navigate to a dedicated creation page. It's especially useful when the create form does not require a lot of fields, to keep the user in the list context — and since Sharp will display a "submit and reopen" button in the modal, the user can quickly create multiple instances.

## Prerequisites

This feature will only work if a Form is defined for the entity (since Sharp will entirely rely on it). 

## Configuration

The configuration is done in the Entity List:

```php
class MyList extends SharpEntityList
{
    public function buildListConfig(): void
    {
        $this->configureQuickCreationForm();
    }
    // ...
}
```

With this, when the user clicks on the "New..." button, a modal will open with the form fields defined in the Form. One common practice is to limit the fields to the strict minimum: this can be achieved by passing an array of field keys to the `configureQuickCreationForm` method:

```php
class MyList extends SharpEntityList
{
    public function buildListConfig(): void
    {
        $this->configureQuickCreationForm(['name', 'price']);
    }
    // ...
}
```

Of course, ensure that these fields are defined in the Form and that all the required fields are present.

## Redirect to the Show Page

When the Form is configured with `configureDisplayShowPageAfterCreation()`, and if the user does not choose to stay in creation (with the "submit and reopen" button), Sharp will redirect to the Show Page after the creation.