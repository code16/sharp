# Authentication

Let's start with authentication, even if this subject seems to be non Sharp related: its is, actually, and without a bit of configuration nothing will work, because Sharp can't be used as a guest.

Sharp uses the standard Laravel authentication.

## Configure user attributes

The Sharp login form asks for a login and a password field; to handle the authentication, Sharp has to know what attributes it must test in your User model. Defaults are `email` and `password`, and can be overriden in the Sharp config:

```php
    // in config/sharp.php
    
    "auth" => [
        "login_attribute" => "login",
        "password_attribute" => "pwd",
        "display_attribute" => "name",
    ]
```

The third attribute, `display_attribute`, is used to display the user name in the Sharp UI. Default is `name`.

## Custom guard

It's very likely that you don't want to authorize all users to access Sharp. You can hook into the the [Laravel custom guards](https://laravel.com/docs/5.4/authentication#adding-custom-guards) functionality, with one config key:

```php
    //in config/sharp.php

    "auth" => [
        "guard" => "sharp",
    ]
```

Of course, this implies that you defined a "sharp" guard in `config/auth.php`, as detailed in the Laravel documentation.

## Authentication check

If you want a simple way to authorize some users to access Sharp in a project where you have other users, you can define an auth check rather than using custom guard.

First write a class which implements `Code16\Sharp\Auth\SharpAuthenticationCheckHandler`:

```php
    class SharpCheckHandler implements SharpAuthenticationCheckHandler
    {
        /**
         * @param $user
         * @return bool
         */
        public function check($user): bool
        {
            return $user->hasGroup('sharp');
        }
    }
```

Perform in the `check()` method any test you need to make on the authenticated user.

Finally enable this feature adding a config key:

```php
    //in config/sharp.php

    "auth" => [
        "check_handler" => \App\Sharp\Auth\SharpCheckHandler::class,
    ]
```

And you are good to go.

---

> next chapter : [Building an Entity List](building-entity-list.md).