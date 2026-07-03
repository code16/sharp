# Upgrading from 9.x to 10.x

## The upgrade command

Sharp 10 comes with an upgrade command that replaces and notifies for deprecated and removed code. You can run it with

```bash
php artisan sharp:upgrade app/Sharp
```

Where `app/Sharp` is the path of your sharp code.

## Updating Dependencies

If you depend on them, you should update the following dependencies in your `composer.json` file :
- `inertiajs/inertia-laravel` to `^3.0` (cf. https://inertiajs.com/docs/v3/getting-started/upgrade-guide)

## Deprecated methods

### Forms

#### Upload `setImageCompactThumbnail()` has no impact and can be removed
```php
\Code16\Sharp\Form\Fields\SharpFormUploadField::make('upload')
  ->setImageCompactThumbnail() // [!code --]
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
