# Testing with Sharp

Sharp provides a few assertions and helpers to help you test your Sharp code.

## The `SharpAssertions` trait

The `Code16\Sharp\Utils\Testing\SharpAssertions` trait is intended to be used in a Feature test.

```php
class PostFormTest extends TestCase
{
    use SharpAssertions;
    
    // ...
}
```

### Helpers

The trait adds a few helpers:

#### `loginAsSharpUser($user)`

Logs in the given user as a Sharp user.

#### `getSharpShow(string $entityKey, $instanceId)`

Call the Sharp API to display the Show Page for the Entity `$entityKey` and instance `$instanceId`.

#### `getSharpForm(string $entityKey, $instanceId = null)`

Call the Sharp API to display the Form for the Entity `$entityKey`. If `$instanceId` is provided, it will be an edit form, and otherwise a creation one.

#### `updateSharpForm(string $entityKey, $instanceId, array $data)`

Call the Sharp API to update the Entity `$entityKey` of id `$instanceId`, with `$data`.

#### `storeSharpForm(string $entityKey, array $data)`

Call the Sharp API to store a new Entity `$entityKey` with `$data`.

#### `deleteSharpEntityList(string $entityKey, $instanceId)`

Call the Sharp API to delete an `$entityKey` instance on the Entity List.

#### `deleteSharpShow(string $entityKey, $instanceId)`

Call the Sharp API to delete an `$entityKey` instance on the Show Page.

#### `callSharpEntityCommandFromList(string $entityKey, string $commandKeyOrClassName, array $data, ?string $commandStep = null)`

Call the `$commandKeyOrClassName` Entity Command with the optional `$data`.

#### `callSharpInstanceCommandFromList(string $entityKey, $instanceId, string $commandKeyOrClassName, array $data, ?string $commandStep = null)`

Call the `$commandKeyOrClassName` Instance Command with the optional `$data`.

#### `callSharpInstanceCommandFromShow(string $entityKey, $instanceId, string $commandKeyOrClassName, array $data, ?string $commandStep = null)`

Call the `$commandKeyOrClassName` Instance Command with the optional `$data`.

#### `withSharpCurrentBreadcrumb(array $breadcrumb): self`

It can be useful to fake a Sharp context before calling a Sharp endpoint, and that's the purpose of this method;  `$breadcrumb` is an array of arrays, each one containing, in this order:
 - a Sharp page type: "list", "show" or "form"
- an entityKey
- (optional) an instanceId

For instance:

```php
    $this
        ->withSharpCurrentBreadcrumb([
            ['list', 'trees'],
            ['show', 'trees', 8],
            ['show', 'leaves', 16],
            ['form', 'leaves', 16],
        ])
        ->getSharpForm(/*...*/)
        ->assertOk();
```
