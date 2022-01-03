---
sidebarDepth: 3
---

# The entity class

An `entity` is simply a data structure which has a meaning in the application context. For instance, a `Person`, a `Post` or an `Order`. It's typically a Model — but it's not necessarily a 1-1 relationship, a Sharp `entity` can represent a portion of a Model, or several Models.

The `entity class` is the place where you can declare the entity configuration: its Entity List, Form, Show Page...

## Write the class

The class must extend `Code16\Sharp\Entities\SharpEntity`. The easiest way to declare your attached classes is to simply override a bunch of protected attributes: 

```php
class SpaceshipEntity extends SharpEntity
{
    protected ?string $list = SpaceshipSharpList::class;
    protected ?string $show = SpaceshipSharpShow::class;
    protected ?string $form = SpaceshipSharpForm::class;
    protected string $label = "Spaceship";
```

Here is the full list:
- `$list`, `$show`, `$form` and `$policy` may be set to a full classname of a corresponding type. The following sections of this documentation describe all this in detail, allowing you to build your Sharp backend.
- `string $label` is used in the breadcrumb, as a default ([see the breadcrumb documentation for more on this](sharp-breadcrumb.md)). You can simply put your entity name here.
- `bool $isSingle` must be set only if you are dealing [with a single show](single-show.md)
- and finally `array $prohibitedActions` can be use to set globally prohibited actions on the entity, [as documented here](entity-authorizations.md).

### Override methods instead

If you need more control, you can override these instead of the attributes:

```php
protected function getList(): ?string {}
protected function getShow(): ?string {}
protected function getForm(): ?string {}
protected function getPolicy(): string|SharpEntityPolicy|null {}
```

Note that the last one, getPolicy, allows you to return a `SharpEntityPolicy` implementation instead of a classname, as it's sometimes easier to declare a quick policy right in here. Fot instance:

```php
class MyEntity extends SharpEntity
{
    // [...]

    protected function getPolicy(): string|SharpEntityPolicy|null
    {
        return new class extends SharpEntityPolicy
        {
            public function update($user, $instanceId): bool
            {
                return $user->isBoss();
            }
        };
    }
}
```

### Handle Multiforms

This is a dedicated topic, [documented here](multiforms.md).