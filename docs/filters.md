# Filters

Filters are a simple way for the user to filter list items or dashboard (see below) widgets on some attribute, like for instance display only books that cost more than 15 euros.

This documentation is written for the EntityList case, but the API is the same for Dashboard (as explained at the end of this page).

## Generator

```sh
php artisan sharp:make:list-filter <class_name> [--required,--multiple]
```

## Write the filter class

First, we need to write a class which implements the `Code16\Sharp\EntityList\EntityListFilter` interface, and therefore declare a `values()` function. This function must return an ["id" => "label"] array. For instance, with Eloquent:

```php
    class SpaceshipTypeFilter implements EntityListFilter
    {
        public function values()
        {
            return SpaceshipType::orderBy("label")
                ->pluck("label", "id");
        }
    }
```


## Configure the filter

Next, in the EntityList, we add the filter in the configuration:

```php
    function buildListConfig()
    {
        $this->setInstanceIdAttribute("id")
            [...]
            ->addFilter("type", SpaceshipTypeFilter::class);
    }
```

Sharp will display a dropdown with those values for a "type" filter.


## Handle filter selection

Once the user clicked on a filter, Sharp will call EntityList's `getListData(EntityListQueryParams $params)` with the filter value in param, which can be grabbed with `$params->filterFor("key")`. For instance:

```php
    function getListData(EntityListQueryParams $params)
    {
        $spaceships = Spaceship::select("spaceships.*")->distinct();

        if($params->filterFor("type")) {
            $spaceships->where("type_id", $params->filterFor("type"));
        }

        [...]
    }
```

## Required filters

Sometimes we'd like to have a filter which can't be null. Just implement `Code16\Sharp\EntityList\EntityListRequiredFilter` subinterface:

```php
    class SpaceshipTypeFilter implements EntityListRequiredFilter
    {

        public function values()
        {
            return SpaceshipType::orderBy("label")
                ->pluck("label", "id");
        }

        public function defaultValue()
        {
            return SpaceshipType::orderBy("label")->first()->id;
        }
    }
```

You'll have to declare another function, `defaultValue()`, which must return the id of the default filter if nothing selected.


## Multiple filter

First, notice that you can have as many filters as you want for an EntityList. The "multiple filter" here designate something else: allowing the user to select more than one value for a filter. To achieve this, simply replace the interface implemented with `Code16\Sharp\EntityList\EntityListMultipleFilter`, and that's it.

In this case, with Eloquent for instance, your might have to modify your code to ensure that you have an array (Sharp will return either null, and id or an array of id, depending on the user selection):

```php
    if ($params->filterFor("pilots")) {
        $spaceships->whereIn(
            "pilots.id",
            (array)$params->filterFor("pilots")
        );
    }
```

Note that a filter can't be required AND multiple.

## Filter label

To use a custom label for the filter, simply add a `label()` function that returns a string in the Filter class.

```php
    public function label()
    {
        return "My label";
    }
```

## Filter search

If you need to add a search textfield on top of your filter list, it's as simple as defining this function:

```php
    public function isSearchable()
    {
        return true;
    }
```

## Filter template

Sometimes you need your filter results to be a little more than a label. For this, use a template; first define a `template()` function in the Filter's class:

```php
    public function template()
    {
        return "{{label}}<br><small>{{detail}}</small>";
    }
```

You can also, for more control, return a view here.

The template will be [interpreted by Vue.js](https://vuejs.org/v2/guide/syntax.html), meaning you can add data placeholders, DOM structure but also directives, and anything that Vue will parse. It's the same as [Autocomplete's templates](form-fields/autocomplete.md).

You'll need also to change your `values()` function, returning more than an ["id"=>"value"] array. For instance:

```php
    public function values()
    {
        return SpaceshipType::orderBy("label")
            ->get()
            ->map(function($type) {
              return [
			    "id" => $type->id,
			    "label" => $type=>label,
			    "detail" => $type->detail_text
              ];
            });
    }
```

Note that **the label attribute is mandatory**: it is used for the result display of the filter.

Finally, if your filter is also searchable, you'll need a `searchKeys()` function to define which attributes should be searched in the template:

```php
    public function searchKeys(): array
    {
        return ["label", "detail"];
    }
```

## Filter set callback

The third and optional `addFilter` argument is a Closure, used as a set value callback.

```php
    $this->addFilter("pilots", PilotFilter::class, function($value, EntityListQueryParams $params) {
        // Filter was set to $value. Do stuff, like putting a value in session.
    });
```

## Master filter

In some cases (like linked filters, for instance: the second filter values depends on the first one), you want to ensure that selecting a filter value will reset all other filters. It's called: "master", and you only need to add a `isMaster()` function in your Filter handler:

```php
    public function isMaster(): bool
    {
        return true;
    }
```

## Retained filters value in session

Sometimes you'll want to make the filter's value persistent across calls. Say for example that you have a "country" filter, which is common to several Entity Lists: the idea is to keep the user choice even when he changes the current displayed list.

To do that, add a `retainValueInSession()` function to your filter:

```php
 class CountryFilter implements EntityListFilter
 {
     public function values()
     {
         [...]
     }

     public function retainValueInSession()
     {
         return true;
     }
 }
 ```

And that's it, Sharp will keep the filter value in session and ensure it is valued on next requests (if not overridden). This feature works for all types of filters (required, multiple).

**Warning**: in order to make this feature work, since filters are generalized, you'll need to have unique filters name.

## Filters for Dashboards

[Dashboards](dashboard.md) can too take advantage of filters; the API is almost the same, here's the specifics:

- There is obviously no Entity or Instance distinction: the only available option are `Code16\Sharp\Dashboard\DashboardFilter`, `Code16\Sharp\Dashboard\DashboardMultipleFilter` and `Code16\Sharp\Dashboard\DashboardRequiredFilter`.
- Filters must be declared in the `buildDashboardConfig()` method of the Dashboard.
- And finally, Sharp will not call `getListData(EntityListQueryParams $params)` but `buildWidgetsData(DashboardQueryParams $params)`. The API is the same, meaning we can call `$params->filterFor('...')`.

## Global menu Filters

Sometimes you may want to "scope" the entire data set. An example could be a user which can manage several organizations.

Instead of adding a filter on almost every Entity List, in this case, you can define a global filter, which will appear like this (on the left menu):

![Example](img/global-filter.png)

To achieve this, first write the filter class, like any filter, except it must implement `\Code16\Sharp\Utils\Filters\GlobalRequiredFilter` — meaning it must be a required filter.

```php
class OrganizationGlobalFilter implements GlobalRequiredFilter
{

    public function values()
    {
        return Organization::orderBy("name")
            ->pluck("name", "id")
            ->all();
    }

    public function defaultValue()
    {
        return Organization::first()->id;
    }
}
```

And then, we declare it in Sharp's config file:

```php
// sharp.php

return [
    [...]

    "global_filters" => [
        "organization" => OrganizationGlobalFilter::class
    ],

    [...]
];
```

Finally, to get the actual value of the filter on your Entity List or Form classes, you must use `SharpContext`:

```php
app(SharpContext::class)->globalFilterFor('organization')
```

The usage of SharpContext is [detailed here](context.md).

---

> Next chapter : [Commands](commands.md)
