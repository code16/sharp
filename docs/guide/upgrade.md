# Upgrading from 9.x to 10.x

## Updating Dependencies

You should update the following dependencies in your `composer.json` file:
- `inertiajs/inertia-laravel` to `^3.0` (cf. https://inertiajs.com/docs/v3/getting-started/upgrade-guide)

## Deprecated features and methods have been removed

Several features and methods that were deprecated in Sharp 9.0 have been removed to clean up the codebase.

### General
- The `currentSharpRequest()` helper and its associated `CurrentSharpRequest` class were removed. Use `sharp()->context()` instead.
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
  - `\Code16\Sharp\Http\Middleware\Api\BindSharpValidationResolver`

- In `SharpFormUploadField`, the following deprecated methods were removed:
  - `setCropRatio()` (use `setImageCropRatio()`)
  - `shouldOptimizeImage()` (use `setImageOptimize()`)
  - `setCompactThumbnail()` (use `setImageCompactThumbnail()`)
  - `setTransformable()` (use `setImageTransformable()`)
  - `setFileFilterImages()` (use `setImageOnly()`)
  - `setFileFilter()` (use `setAllowedExtensions()`)
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

### Renaming
The following classes have been renamed / moved:
- `\Code16\Sharp\EntityList\Filters\EntityListCheckFilter` to `\Code16\Sharp\Filters\CheckFilter`
- `\Code16\Sharp\EntityList\Filters\EntityListDateRangeFilter` to `\Code16\Sharp\Filters\DateRangeFilter`
- `\Code16\Sharp\EntityList\Filters\EntityListDateRangeRequiredFilter` to `\Code16\Sharp\Filters\DateRangeRequiredFilter`
- `\Code16\Sharp\EntityList\Filters\EntityListSelectFilter` to `\Code16\Sharp\Filters\SelectFilter`
- `\Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter` to `\Code16\Sharp\Filters\SelectMultipleFilter`
- `\Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter` to `\Code16\Sharp\Filters\SelectRequiredFilter`
- `\Code16\Sharp\Utils\Filters\CheckFilter` to `\Code16\Sharp\Filters\CheckFilter`
- `\Code16\Sharp\Utils\Filters\DateRangeFilter` to `\Code16\Sharp\Filters\DateRangeFilter`
- `\Code16\Sharp\Utils\Filters\DateRangeRequiredFilter` to `\Code16\Sharp\Filters\DateRangeRequiredFilter`
- `\Code16\Sharp\Utils\Filters\SelectFilter` to `\Code16\Sharp\Filters\SelectFilter`
- `\Code16\Sharp\Utils\Filters\SelectMultipleFilter` to `\Code16\Sharp\Filters\SelectMultipleFilter`
- `\Code16\Sharp\Utils\Filters\SelectRequiredFilter` to `\Code16\Sharp\Filters\SelectRequiredFilter`
