# Upgrading from 9.x to 10.x

## The upgrade command

Sharp 10 comes with an upgrade command that replaces and notifies for deprecated and removed code. You can run it with:

```bash
php artisan sharp:upgrade app/Sharp
```

Where `app/Sharp` is the base path of your sharp code. Use the `--dry-run` option to list the files that would be updated without writing them.

The command handles most of the changes described below:
- it **rewrites** moved classes and namespaces, renamed methods, Command return types and the `info()` / `link()` boolean arguments;
- it **reports** the files where it detected removed code that must be migrated by hand (like `currentSharpRequest()`, or `execute(): array` methods it could not safely rewrite).

## Updating Dependencies

If you depend on them, you should update the following dependencies in your `composer.json` file :
- `inertiajs/inertia-laravel` to `^3.0` (cf. https://inertiajs.com/docs/v3/getting-started/upgrade-guide)

## Commands

### Commands have moved out of the EntityList namespace

Commands are used everywhere in Sharp (Entity Lists, Show Pages, Dashboards), so they now live in their own namespace. The `ReorderHandler` interface, which is not a command, has moved to the EntityList namespace. *(handled by `sharp:upgrade`)*

```php
use Code16\Sharp\EntityList\Commands\EntityCommand; // [!code --]
use Code16\Sharp\Commands\EntityCommand; // [!code ++]

use Code16\Sharp\EntityList\Commands\Wizards\InstanceWizardCommand; // [!code --]
use Code16\Sharp\Commands\Wizards\InstanceWizardCommand; // [!code ++]

use Code16\Sharp\EntityList\Commands\ReorderHandler; // [!code --]
use Code16\Sharp\EntityList\ReorderHandler; // [!code ++]
```

This applies to all classes of `Code16\Sharp\EntityList\Commands` (`Command`, `EntityCommand`, `InstanceCommand`, `SingleInstanceCommand`, `EntityState`, `SingleEntityState` and the `Wizards` classes), except `Code16\Sharp\EntityList\Commands\QuickCreate\QuickCreationCommand`, which is specific to Entity Lists and stays where it is. `Code16\Sharp\Dashboard\Commands\DashboardCommand` is unchanged as well.

### Commands must return a `CommandReturn`

Command execution methods now return a typed `Code16\Sharp\Commands\Returns\CommandReturn` object instead of an array. Since the `$this->info()`, `$this->reload()`, `$this->refresh()`... helpers are unchanged, you only need to update the return types. *(handled by `sharp:upgrade`)*

```php
use Code16\Sharp\Commands\Returns\CommandReturn; // [!code ++]

class MyCommand extends InstanceCommand
{
    public function execute(mixed $instanceId, array $data = []): array // [!code --]
    public function execute(mixed $instanceId, array $data = []): CommandReturn // [!code ++]
    {
        return $this->reload();
    }
}
```

This applies to `execute()`, `executeSingle()`, and the Wizard `executeFirstStep()` / `executeStep()` / `executeStepXXX()` methods. Entity States have more specific return types:

```php
use Code16\Sharp\Commands\Returns\CommandRefreshReturn; // [!code ++]
use Code16\Sharp\Commands\Returns\CommandReloadReturn; // [!code ++]

// EntityState
protected function updateState($instanceId, string $stateId): ?array // [!code --]
protected function updateState($instanceId, string $stateId): CommandReloadReturn|CommandRefreshReturn|null // [!code ++]

// SingleEntityState
protected function updateSingleState(string $stateId): array // [!code --]
protected function updateSingleState(string $stateId): CommandReloadReturn // [!code ++]
```

Note that `updateSingleState()` can no longer return `null`: it must return `$this->reload()`.

::: warning
`sharp:upgrade` only rewrites classes that directly extend a Sharp Command class. If your Commands extend your own abstract class, the command will report the remaining `execute(): array` methods, which you must update by hand.
:::

### `info()` and `link()` boolean arguments are replaced by fluent modifiers

*(handled by `sharp:upgrade`)*

```php
return $this->info('Invitation sent!', reload: true); // [!code --]
return $this->info('Invitation sent!')->withReload(); // [!code ++]

return $this->link('https://example.org', openInNewTab: true); // [!code --]
return $this->link('https://example.org')->inNewTab(); // [!code ++]
```

Both modifiers accept an optional boolean, for conditional cases: `->withReload($shouldReload)`.

### Testing assertions

A few Command testing assertions were improved (cf. [Testing](testing#asserting-command-results)), with two small behavior changes:
- `assertReturnsInfo('')` and `assertReturnsLink('')` now assert an empty message / link; to assert any value, omit the parameter.
- Response methods called on testing objects now return their value: `->json('message')` returns the message instead of the testing object.

## Deprecated methods

### Forms

Upload `setImageCompactThumbnail()` has no impact and can be removed:
```php
\Code16\Sharp\Form\Fields\SharpFormUploadField::make('upload')
  ->setImageCompactThumbnail() // [!code --]
```

### Testing
The legacy testing API is deprecated and will be removed in 11.x. Please refer to (cf. [Testing](testing)). Here are replacement examples:

```php
uses(\Code16\Sharp\Utils\Testing\SharpAssertions::class)

it('test', function () {
    $this->callSharpEntityCommandFromList(PersonEntity::class, MyCommand::class, ['attr' => 'some_value']) // [!code --]
    $this->sharpList(PersonEntity::class)->entityCommand(MyCommand::class)->post(['attr' => 'some_value']) // [!code ++]
    
    $this->callSharpInstanceCommandFromList(PersonEntity::class, 1, MyCommand::class, ['attr' => 'some_value']) // [!code --]
    $this->sharpList(PersonEntity::class)->instanceCommand(MyCommand::class, 1)->post(['attr' => 'some_value']) // [!code ++]
    
    $this->callSharpInstanceCommandFromShow(PersonEntity::class, 1, MyCommand::class, ['attr' => 'some_value']) // [!code --]
    $this->sharpShow(PersonEntity::class, 1)->instanceCommand(MyCommand::class)->post(['attr' => 'some_value']) // [!code ++]
    
    $this->getSharpShow(PersonEntity::class, 1) // [!code --]
    $this->sharpShow(PersonEntity::class, 1)->get() // [!code ++]
    
    $this->getSharpForm(PersonEntity::class, 1) // [!code --]
    $this->sharpForm(PersonEntity::class, 1)->get() // [!code ++]
    
    $this->getSingleSharpForm(PersonEntity::class) // [!code --]
    $this->sharpForm(PersonEntity::class)->get() // [!code ++]
    
    $this->updateSharpForm(PersonEntity::class, 1, []) // [!code --]
    $this->sharpForm(PersonEntity::class, 1)->update([]) // [!code ++]
    
    $this->storeSharpForm(PersonEntity::class, 1, []) // [!code --]
    $this->sharpForm(PersonEntity::class, 1)->store([]) // [!code ++]
    
    $this->updateSingleSharpForm(PersonEntity::class, []) // [!code --]
    $this->sharpForm(PersonEntity::class)->update([]) // [!code ++] 
    
    $this->withSharpBreadcrumb(function (\Code16\Sharp\Utils\Links\BreadcrumbBuilder $builder) { // [!code --]
        $builder->appendEntityList(PersonEntity::class)->appendShowPage(PersonEntity::class, 1) // [!code --]
    })->getSharpForm(PersonEntity::class, 1) // [!code --]
    $this->sharpList(PersonEntity::class)->sharpShow(PersonEntity::class, 1)->sharpForm(PersonEntity::class)->get() // [!code ++] 
})
```

## Removed classes and methods

Several features and methods that were deprecated in Sharp 9.0 have been removed to clean up the codebase.

### General
- The `currentSharpRequest()` helper and its associated `CurrentSharpRequest` class were removed. Use `sharp()->context()` instead (cf. [Context](context)).
- The `SharpAuthenticationCheckHandler` interface was removed. Use the `viewSharp` Gate instead.
- The `withSharpCurrentBreadcrumb()` method in `SharpAssertions` was removed. Use `withSharpBreadcrumb()` instead.
- Removal of "multi-forms". Replace all `SharpEntity::getMultiforms()` implementations to `SharpEntityList::configureEntityMap()`  instead (cf. [Building entity list](building-entity-list#entity-map)).
- Smart handling of legacy fontaweome icons class has been removed. Tou must convert all `fa-` occurences to `fas-*`, `far-*`, `fab-*`.

### Configuration
- The legacy configuration handling based on the `sharp.php` config file was removed. You must now use a `SharpAppServiceProvider` with a `SharpConfigBuilder`.
- `SharpConfigBuilder::addEntity()` has been removed in favor of `SharpConfigBuilder::declareEntity()`.

### Forms
- The legacy `$formValidatorClass` property handling in `SharpForm` was removed. Implement the `SharpForm::rules()` and `SharpForm::messages()` methods instead (cf. [Building form](building-form#input-validation)). Along with this removal, the following classes have been removed:
  - `\Code16\Sharp\Form\Validator\SharpFormRequest`
  - `\Code16\Sharp\Form\Validator\SharpValidator`
  - `\Code16\Sharp\Http\Middleware\Api\BindSharpValidationResolver` (remove this from config)
- In `SharpFormUploadField`, the following deprecated methods were removed:
```php
\Code16\Sharp\Form\Fields\SharpFormUploadField::make('upload')
  ->setCropRatio() // [!code --]
  ->setImageCropRatio() // [!code ++]
  
  ->shouldOptimizeImage() // [!code --]
  ->setImageOptimize() // [!code ++]
  
  ->setTransformable() // [!code --]
  ->setImageTransformable() // [!code ++]
  
  ->setFileFilterImages() // [!code --]
  ->setImageOnly() // [!code ++]
  
  ->setFileFilter() // [!code --]
  ->setAllowedExtensions() // [!code ++]
```
- The `FormLayoutColum::withSingleField()` & `ShowLayoutColum::withSingleField()`  method was removed. Use `withField()` or `withListField()` instead.
- `SharpFormDateField::setDisplayFormat()` has been removed
- `SharpFormAutocompleteField` was removed. Migrate to the following
    - `SharpFormAutocompleteField::make('key', 'local')` to `SharpFormAutocompleteLocalField::make('key')`
    - `SharpFormAutocompleteField::make('key', 'remote')` to `SharpFormAutocompleteRemoteField::make('key')`

### Entity Lists
- The `setWidthOnSmallScreens()` and `setWidthOnSmallScreensFill()` methods in `EntityListField` were removed as they are no longer used in the new front-end table UI.
- The `configureMultiformAttribute()` method in `SharpEntityList` was removed. Use `configureEntityMap()` instead.

### Show
- The `configureMultiformAttribute()` method in `SharpShow` was removed. It's not used by sharp.

### Filters

The following classes have been renamed / moved:

```php
use \Code16\Sharp\EntityList\Filters\EntityListCheckFilter; // [!code --]
use \Code16\Sharp\Utils\Filters\CheckFilter; // [!code --]
use \Code16\Sharp\Filters\CheckFilter; // [!code ++]

use \Code16\Sharp\EntityList\Filters\EntityListDateRangeFilter; // [!code --]
use \Code16\Sharp\Utils\Filters\DateRangeFilter; // [!code --]
use \Code16\Sharp\Filters\DateRangeFilter; // [!code ++]

use \Code16\Sharp\EntityList\Filters\EntityListDateRangeRequiredFilter; // [!code --]
use \Code16\Sharp\Utils\Filters\DateRangeRequiredFilter; // [!code --]
use \Code16\Sharp\Filters\DateRangeRequiredFilter; // [!code ++]

use \Code16\Sharp\EntityList\Filters\EntityListSelectFilter; // [!code --]
use \Code16\Sharp\Utils\Filters\SelectFilter; // [!code --]
use \Code16\Sharp\Filters\SelectFilter; // [!code ++]

use \Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter; // [!code --]
use \Code16\Sharp\Utils\Filters\SelectMultipleFilter; // [!code --]
use \Code16\Sharp\Filters\SelectMultipleFilter; // [!code ++]

use \Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter; // [!code --]
use \Code16\Sharp\Utils\Filters\SelectRequiredFilter; // [!code --]
use \Code16\Sharp\Filters\SelectRequiredFilter; // [!code ++]
```
