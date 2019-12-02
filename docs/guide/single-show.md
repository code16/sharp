---
sidebarDepth: 3
---

# Using SingleShow for unique resources

Sometimes you will need to configure a "unique" resource that does not fit into a List / Show schema, like for instance an account, or a configuration item. Ti handle this kind of "unique" resource, Sharp provides a way to build SingleShows.


## Generator

```sh
php artisan sharp:make:show <class_name> --single [--model=<model_name>]
```


## Write the class

Instead of extending `SharpShow`, our SingleShow implementation should extend `Code16\Sharp\Show\SharpSingleShow`. We still have to implement `buildShowFields()` and `buildShowLayout()` to declare the fields presenting the instance, an optionally `buildShowConfig()`, but the `find()` method is different:

- `findSingle(): array`, without any parameter because in a single case the functional code has to determine the instance on its side (based on the current user, for instance).


### Single Commands

Declared Commands must also be implemented as *single*. Like for Shows, this only means extending a more specific abstract class: `Code16\Sharp\EntityList\Commands\SingleInstanceCommand`. The two differences with regular `InstanceCommand` are:

- `executeSingle(array $data = []): array`, which does not take any `$instanceId` is parameter
- `authorize(): bool`, in case you need to define an specific authorization, instead of `authorizeFor($instanceId)`.


### Single EntityState

Same for EntityState: in a `SingleShow` case, you must implement EntityState as a `Code16\Sharp\EntityList\Commands\SingleEntityState`, which differs a bit:

- `updateSingleState(string $stateId)`
- `authorize(): bool`


## Linking a SingleShow to the main menu

SingleShow can of course be linked in the menu:

```php
config/sharp.php

return [
    [...]
    "entities" => [
        [...],
        "account" => [
            "show" => AccountSingleSharpShow::class,
            "form" => AccountSingleSharpForm::class,
    	]
    ],
    "menu" => [
        [...],
        [
            "label" => "My account",
            "icon" => "fa-user",
            "entity" => "account",
            "single" => true
        ]
    ]
];
```

Nothing different on the `entities` part, but do note the `"single" => true` in the `menu` to tell Sharp to treat this entity as a SingleShow.

## What if you need a Form?

Well, that's a [SingleForm](single-form.md) then.