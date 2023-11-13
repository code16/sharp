---
sidebarDepth: 3
---

# Using Single Show for unique resources

Sometimes you will need to configure a "unique" resource that does not fit into a List / Show schema, like for instance an account, or a configuration item. To handle this kind of "unique" resource, Sharp provides a way to build Single Shows.

## Write the class

Instead of extending `SharpShow`, our SingleShow implementation should extend `Code16\Sharp\Show\SharpSingleShow`. We still have to implement `buildShowFields(FieldsContainer $showFields)` and `buildShowLayout(ShowLayout $showLayout)` to declare the fields presenting the instance, an optionally `buildShowConfig()`, but the `find()` method is different:

- `findSingle(): array`, without any parameter because in a single case the functional code has to determine the instance on its side (based on the current user, for instance).

```php
class ProfileSingleShow extends SharpSingleShow
{
   // [...]

    public function findSingle(): array
    {
        return $this->transform(auth()->user());
    }
}
```

## Single Show declaration

We must declare in the entity class that we want to use a Single Show:

```php
class ProfileEntity extends SharpEntity
{
    protected bool $isSingle = true;
    protected string $label = 'My profile';
    protected ?string $show = ProfileSingleShow::class;
    protected ?string $form = ProfileSingleForm::class;
}
```

Notice the `$isSingle` property, which indicates that this entity does not have an Entity List.

## Single Commands

Declared Commands must also be implemented as *single*. Like for Shows, this only means extending a more specific abstract class: `Code16\Sharp\EntityList\Commands\SingleInstanceCommand`. The two differences with regular `InstanceCommand` are:

- `executeSingle(array $data = []): array`, which does not take any `$instanceId` is parameter
- `authorize(): bool`, in case you need to define a specific authorization, instead of `authorizeFor($instanceId)`.

## Single EntityState

Same for EntityState: in a `SingleShow` case, you must implement EntityState as a `Code16\Sharp\EntityList\Commands\SingleEntityState`, which differs a bit:

- `updateSingleState(string $stateId)`
- `authorize(): bool`

## What if you need a Form?

Well, that's a [SingleForm](single-form.md) then.