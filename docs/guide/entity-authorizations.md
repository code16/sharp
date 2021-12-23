# Entity authorizations

You can check documentation of authorizations for [Commands](commands.md) or [Entity States](entity-states.md). Here we are going to see how we can define authorizations for an entity.

## Available permissions

Entities have five permission keys:

- `entity`: to see the entity in the side-menu, and to display its list or single show page. Without this, the entity is hidden to the user.
- `view`: without this, the user can access the Entity list, but not the Show Page nor the Form.
- `update`: without this, the user can't access the Form.
- `create`: without this, the user can't display the create form.
- `delete`: without this, the user can't delete an instance.


## Globally prohibited actions

As a first step, in some cases you may want to forbid some actions to anyone: just an application rule, like "no one can delete an Order", or "no one can edit a User".

For this add the permission keys in the `$prohibitedActions` attribute og the Entity class:

```php
class UserEntity extends SharpEntity
{
    protected ?string $list = UserSharpList::class;
    protected array $prohibitedActions = [
        "delete", "create"
    ];
    // ...
}
```

Note that you can't define here the `entity` permission.

## Policies

For user-based rules, create a `Policy` class which is just a plain class defining methods for some (or all) permissions.

### Write the class

It must extend `Code16\Sharp\Auth\SharpEntityPolicy`:

```php
class SpaceshipPolicy extends SharpEntityPolicy
{
    public function entity($user): bool
    {
        return sharp_user()->hasGroup('admin');
    }

    public function view($user, $instanceId): bool
    {
        return sharp_user()->owner_id == $user->id;
    }

    public function update($user, $instanceId): bool
    {
        // ...
    }

    public function delete($user, $instanceId): bool
    {
        // ...
    }

    public function create($user): bool
    {
        // ...
    }
}
```

Only write methods which don't return true, as this is the default behaviour.

### Configure the policy

The policy must be declared in the [Entity class](entity-class.md):

```php
class SpaceshipEntity extends SharpEntity
{
    protected ?string $policy = SpaceshipSharpPolicy::class;
    // ...
}
```

### Policies for Dashboards

It's all the same, except Dashboards have only one method: `public function entity($user): bool`.