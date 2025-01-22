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

#### `getSharpSingleForm(string $entityKey)`

Call the Sharp API to display the edit Form for the single Entity `$entityKey`.

#### `updateSharpForm(string $entityKey, $instanceId, array $data)`

Call the Sharp API to update the Entity `$entityKey` of id `$instanceId`, with `$data`.

#### `updateSharpSingleForm(string $entityKey, array $data)`

Call the Sharp API to update the single Entity `$entityKey` with `$data`.

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

#### `withSharpBreadcrumb(Closure $callback): self`

Most of the time, the breadcrumb automatically set by Sharp is enough. But sometimes it can be useful to define a whole Sharp context before calling an endpoint, and that's the purpose of this method. The `$callback` contains a built instance of Code16\Sharp\Utils\Links\BreadcrumbBuilder, which can be used like this:

```php
it('allow the user to display a leaf form', function () {
    $this
        ->loginAsSharpUser()
        ->withSharpBreadcrumb(function (BreadcrumbBuilder $builder) {
            return $builder
                ->appendEntityList('trees')
                ->appendShowPage('trees', 6)
                ->appendShowPage('leaves', 16);
        })
        ->getSharpForm('leaves', 16)
        ->assertOk();
});
```
