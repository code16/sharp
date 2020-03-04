# Getting started

## Terminology, general concept

In Sharp, we handle `entities`; and `entity` is simply a data structure which has a meaning in the applicative context. For instance, a `Person`, a `Post` or an `Order`. In the Eloquent world, for which Sharp is optimized, it's typically a Model — but it's not necessarily a 1-1 relationship, a Sharp `entity` can represent a portion of a Model, or several Models.

Each instance of an `entity` is called... an `instance`.

Each `entity` in Sharp can be displayed:
- in an `Entity List`, which is the list of all the `instances` for this `entity`: with some configuration and code, the user can sort the data, add filters, and perform a search. From there we also gain access to applicative `commands` applied to an `instance` or the whole list, and to a simple `state` changer (the publish state of an Article, for instance). All of that is described below.
- In a `Show`, optionally, to display an `instance` details.
- And in a `Form`, either to update or create a new `instance`.

## Installation

Sharp 4 needs Laravel 5.5+ and PHP 7.1.3+.

- Add the package with composer: `composer require code16/sharp`
- And then publish assets: `php artisan vendor:publish --provider="Code16\Sharp\SharpServiceProvider" --tag=assets`

A tip on this last command: you'll need fresh assets each time Sharp is updated, so a good practice is to add the command in the `scripts.post-autoload-dump` section of your `composer.json` file:

```json
"scripts": {
    [...]
    "post-autoload-dump": [
        "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
        "@php artisan vendor:publish --provider=Code16\\Sharp\\SharpServiceProvider --tag=assets --force",
        "@php artisan package:discover"
    ]
},
```

## Configuration

Sharp needs a `config/sharp.php` config file, mainly to declare `entities`. 

You can init this file with: `php artisan vendor:publish --provider="Code16\Sharp\SharpServiceProvider" --tag=config`

Here's an example:

```php
return [
    "entities" => [
        "spaceship" => [
            "list" => \App\Sharp\SpaceshipSharpList::class,
            "form" => \App\Sharp\SpaceshipSharpForm::class,
            "show" => \App\Sharp\SpaceshipSharpShow::class,
            "validator" => \App\Sharp\SpaceshipSharpValidator::class,
            "policy" => \App\Sharp\Policies\SpaceshipPolicy::class
        ]
    ]
];
```

As we can see, each `entity` (like `spaceship`, here), can define:

- a `list` class, responsible for the `Entity List`,
- a `show` class, responsible for displaying an `instance`,
- a `form` class, responsible for... the `Form`
- a `validator` class, to handle form validation
- and a `policy` class, for authorization.

Almost each one is optional, in fact: we could skip the `show` and go straight to the `form` from the `list`, for instance. 

We'll get into all those classes in this document. The important thing to notice is that Sharp provides base classes to handle all the wiring (and more), but as we'll see, the applicative code is totally up to you.

## Access to Sharp

Once installed, Sharp is accessible via the url `/sharp`, by default. If you wish to change this default value, you'll need to define the `custom_url_segment` config value, in `config/sharp.php`:

```php
return [
    "name" => "Saturn",
    "custom_url_segment" => "admin",
    [...]
]
```