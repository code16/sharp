# Dashboard

Class: `Code16\Sharp\Show\Fields\SharpShowDashboardField`.

The field allows you to integrate a [Dashboard](../building-dashboard.md) into your Show Page.

## Constructor

This field needs, as first parameter, either the entity key or the `SharpDashboardEntity` class that declares the dashboard which will be included in the Show Page.

For instance:

```php
SharpShowDashboardField::make('posts_dashboard')
```

or

```php
SharpShowDashboardField::make(PostDashboardEntity::class)
```
::: warning
This last syntax is better in terms of DX (since it allows using the IDE to navigate to the Entity List implementation), but it won’t work in two specific cases: if you use a custom `SharpEntityResolver` or if you your Entity is declared with multiple keys.
:::

## Configuration

### `hideFilterWithValue(string $filterName, $value)`
This is the most important method of the field, since it will not only hide a filter but also set its value. The purpose is to allow to **scope the data to the instance** of the Show Page. For example, let’s say we display a Post and that we want to embed a dashboard with the post's statistics:


```php
class PostShow extends SharpShow
{
    // ...
    
    public function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(
            SharpShowDashboardField::make(PostDashboardEntity::class)
                ->hideFilterWithValue(PostFilter::class, 64)
        );
    }
}
```

Here we're scoping the `PostDashboard` declared in the `PostDashboardEntity` to the instance of the `Post` with id 64. 


You can pass a closure as the value, and it will contain the current Show instance id. In most cases, you'll have to write this:

```php
SharpShowDashboardField::make(PostDashboardEntity::class)
    ->hideFilterWithValue(PostFilter::class, fn ($instanceId) => $instanceId);
```

**One final note**: sometimes the linked filter is really just a scope, never displayed to the user. In this case, it can be tedious to write a full implementation in the Dashboard. In this situation, you can use the `HiddenFiler` class for the filter, passing a key:

```php
class PostShow extends SharpShow
{
    // ...
    
    public function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(
            SharpShowDashboardField::make(PostDashboardEntity::class)
                ->hideFilterWithValue('post', fn ($instanceId) => $instanceId);
        );
    }
}
```

```php
use \Code16\Sharp\EntityList\Filters\HiddenFilter;

class PostDashboard extends SharpDashboard
{
    // ...

    protected function getFilters(): ?array
    {
        return [
            HiddenFilter::make('post')
        ];
    }
    
    protected function buildWidgetsData(): void
    {
        return $this->setFigureData('visit_count', 
            figure: Post::query()
                ->findOrFail($this->queryParams->filterFor('post'))
                ->get()?->visit_count
        );
    }
}
```

### `hideDashboardCommand(array|string $commands): self`

Use it to hide any dashboard command in this particular Dashboard (useful when reusing a Dashboard). This will apply before looking at authorizations.

## Display in layout

To display your dashboard in your show page's layout, you have to use the `addDashboardSection()` method in your Show Page's `buildShowLayout()` method.

```php
protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection(function (ShowLayoutSection $section) {
                $section
                    ->addColumn(7, function (ShowLayoutColumn $column) {
                        $column
                            ->withFields(categories: 5, author: 7)
                           // ...
                    })
                    ->addColumn(5, function (ShowLayoutColumn $column) {
                        // ...
                    });
            })
            ->addDashboardSection(PostDashboardEntity::class);
    }
```
