# Filters

Filters provide a way for the user to filter list items or dashboard widgets on some attribute; for instance, display only books that cost more than 15 euros.

This documentation is written for the Entity List case, but the API is the same for Dashboard (as explained at the end of this page).

## Generator

```bash
php artisan sharp:make:entity-list-filter <class_name> [--required,--multiple,--date-range,--check]
```

## Write the filter class

First, we need to write a class which extends `Code16\Sharp\Filters\SelectFilter`, and therefore declare a `values()` function. This function must return an `[{id} => {label}]` array. For instance, with Eloquent:

```php
class ProductCategoryFilter extends SelectFilter
{
    public function values(): array
    {
        return ProductCategory::orderBy('label')
            ->pluck('label', 'id')
            ->toArray();
    }
}
```

## Configure the filter

You can implement the optional `buildFilterConfig()` method to configure the filter:

```php
class ProductCategoryFilter extends SelectFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Category')
            ->configureKey('cat')
            ->configureRetainInSession();
    }
    
    // ...
}
```

- `configureLabel(string $label)`: use this to define the filter label displayed in the UI.
- `configureKey(string $key)`: the default key, meaning the identifier, of a filter is its class name. If you need to change this (which should be a rare case), you can do so with this method.
- `configureRetainInSession()`: to keep the filter value in session (see below).

## Declare the filter

Next, in the Entity List, we must declare the filter:

```php
class ProductEntityList extends SharpEntityList
{
    function getFilters(): ?array
    {
        return [
            ProductCategoryFilter::class,
        ];
    }
    
    // ...
}
```

## Handle filter selection

Once the user clicked on a filter, Sharp will call EntityList's `getListData()`; the filter value will be accessible with:
- its classname : `$this->queryParams->filterFor(MyFilter::class)`
- or its custom key, if defined with `configureKey()`: `$this->queryParams->filterFor('key')`

Example:

```php
class ProductList extends SharpEntityList
{
    public function getListData(): array|Arrayable
    {
        $products = Product::query();
    
        if ($cat = $this->queryParams->filterFor(ProductCategoryFilter::class)) {
            $products->where('category_id', $cat);
        }
    
        // ...
    }
    
    // ...
}
```

## Multiple filter

First, notice that you can have as many filters as you want for an EntityList. The "multiple filter" here designate something else: allowing the user to select more than one value for a filter. To achieve this, make your filter extend `Code16\Sharp\Filters\SelectMultipleFilter`.

In this case, with Eloquent for instance, your might have to modify your code to ensure that you have an array (Sharp will return either null, and id or an array of id, depending on the user selection):

```php
class ProductList extends SharpEntityList
{
    public function getListData(): array|Arrayable
    {
        $products = Product::query();
    
        if ($categories = $this->queryParams->filterFor(ProductCategoriesFilter::class)) {
            $products->whereIn('category_id', $categories);
        }
    
        // ...
    }
    
    // ...
}
```

Note that a filter can't be required AND multiple.

## Date range filter

You might find useful to filter list elements on a specific date range. Date range filters enable you to show only data that meets a given time period. To implement such a filter, your filter class must extend `Code16\Sharp\Filters\DateRangeFilter`.

Then you need to adjust the query with selected range; in this case, with Eloquent for instance, you might add a condition like:

```php
class ProductList extends SharpEntityList
{
    public function getListData(): array|Arrayable;
    {
        $products = Product::query();
        
        if ($range = $this->queryParams->filterFor(ProductCreationDateFilter::class)) {
            $products->whereBetween('created_at', [$range->getStart(), $range->getEnd()]);
        }
        
        // ...
    }
    
    // ...
}
```

### Configuration

You can define the date display format (default is `MM-DD-YYYY`, using [Carbon isoFormat() syntax](https://carbon.nesbot.com/docs/#iso-format-available-replacements)) and choose if the week should start on monday (default is sunday).
With `configureShowPresets()`, a list of buttons is displayed allowing the user to quickly select a date range.

```php
class ProductCreationDateFilter extends DateRangeFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureDateFormat("YYYY-MM-DD")
            ->configureMondayFirst(false)
            ->configureShowPresets();
    }
    
    // ...
}
```

## Autocomplete remote filter

If you want to use a remote filter, you can use the `Code16\Sharp\Filters\AutocompleteRemoteFilter` class. It is very similar to the `Code16\Sharp\Filters\SelectFilter` class, but it uses a remote endpoint to fetch the values.

```php
class ProductCategoryFilter extends AutocompleteRemoteFilter
{
    public function buildFilterConfig(): void
    {
        $this
            ->configureLabel('Category')
            ->configureDebounceDelay(200) // 300ms per default
            ->configureSearchMinChars(2); // 1 per default, set 0 to search directly on opening the filter
    }
    
    public function values(string $query): array
    {
        return ProductCategory::orderBy('label')
            ->where('label', 'like', "%$query%")
            ->pluck('label', 'id')
            ->toArray();
    }

    public function valueLabelFor(string $id): ?string
    {
        return ProductCategory::find($id)?->label;
    }
}
```

The `values()` method must return an `[{id} => {label}]` array. The `valueLabelFor()` method is used to display the label in the dropdown for the selected id.


## Required filters

It is sometimes useful to have a filter which can't be null: to achieve this you need to extend the right "Required" subclass (`SelectRequiredFilter` or `DateRangeRequiredFilter`), and define a proper default value.

Example for a select filter:

```php
class ProductCategoryFilter extends SelectRequiredFilter
{
    public function defaultValue(): mixed
    {
        return ProductCategory::orderBy('label')->first()->id;
    }
    
    // ...
}
```

Note that a filter can't be required AND multiple.

Example for a date range filter:

```php
class ProductCreationDateFilter extends DateRangeRequiredFilter
{
    public function defaultValue(): array
    {
        return [
            'start' => Carbon::yesterday(),
            'end' => Carbon::today(),
        ];
    }
    
    // ...
}
```

## Filter search

If you want your select filter to be searchable via a search text field, you can use:

```php
public function buildFilterConfig(): void
{
    $this->configureSearchable();
}
```

## Check filter

In case of a filter that is just a matter on true / false ("only show admins" for example), just make your filter class extend `Code16\Sharp\Filters\CheckFilter`.

## Master filter

In some cases you want to ensure that selecting a filter value will reset all other filters. It's called "master filter".

```php
public function buildFilterConfig(): void
{
    $this->configureMaster();
}
```

## Retained filters value in session

If you want to make the filter's value persistent across calls you can leverage the "retain filter" feature. For example, you may have a "country" filter which is common to several Entity Lists: the idea is to keep the user choice even when he changes the current displayed list.

```php
public function buildFilterConfig(): void
{
    $this->configureRetainInSession();
}
```

And with that Sharp will keep the filter value in session and ensure it is valued on next requests (if not overridden). This feature works for all types of filters (required, multiple).

::: warning 
In order to make this feature work, since filters are generalized, you'll need to have unique filters key (the filter class name by default).
:::

## Drop filters depending on functional data

Sometimes you may want to hide a filter to the user depending on the actual data, or on other filters values. This can be achieved by using the `hideFilter()` method in your EntityList class, typically in the `getListData()` method.

```php
class OrderList extends SharpEntityList
{
    protected function getFilters(): ?array
    {
        return [
            PaymentMethodFilter::class,
            OnlinePaymentProviderFilter::class,
        ];
    }
    
    public function getListData(): array|Arrayable
    {
        if ($this->queryParams->filterFor(PaymentMethodFilter::class) !== 'online') {
            // No need to show the OnlinePaymentProviderFilter
            $this->hideFilter(OnlinePaymentProviderFilter::class);
        }
        
        // ...
    }
    
    // ...
}
```

## Filters for Dashboards

[Dashboards](building-dashboard.md) also can take advantage of filters; the API the same, but base classes are specific: `Code16\Sharp\Filters\SelectFilter`, `Code16\Sharp\Filters\DateRangeFilter`,`Code16\Sharp\Filters\CheckFilter` and so on.

## Global menu Filters

You may want to "scope" the entire data set: an example of this could be a user which can manage several organizations. Instead of adding a filter on almost every Entity List, in this case, you can define a global filter, which will appear on top of the global menu.

To achieve this, first write the filter class, like any filter, except it must
extend `\Code16\Sharp\Filters\GlobalRequiredFilter` — meaning it must be a required filter.

```php
class OrganizationGlobalFilter extends GlobalRequiredFilter
{
    public function values(): array
    {
        return Corporation::orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function defaultValue(): mixed
    {
        return Corporation::first()->id;
    }
    
    public function authorize(): bool
    {
        // Optional: you can define an authorization logic here
        return true;
    }
}
```

And then, we declare it:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->addGlobalFilter(OrganizationGlobalFilter::class)
            // ...
    }
}
```

Finally, to get the actual value of the filter on your Entity List, Show Page or Form classes, you must use the context:

```php
sharp()->context()->globalFilterValue(OrganizationGlobalFilter::class)
```

The usage of Sharp Context is [detailed here](context.md).
