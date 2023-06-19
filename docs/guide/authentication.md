# Authentication

Let's start with authentication, even if this subject seems to be non Sharp related: it is, actually, and without a bit of configuration nothing will work, because Sharp can't be used as a guest.

Sharp uses the standard Laravel authentication.

## Configure user attributes

The Sharp login form asks for a login and a password field; to handle the authentication, Sharp has to know what attributes it must test in your User model. Defaults are `email` and `password`, and can be overridden in the Sharp config:

```php
// in config/sharp.php

"auth" => [
    'login_attribute' => 'login',
    'password_attribute' => 'pwd',
    'display_attribute' => 'name',
]
```

The third attribute, `display_attribute`, is used to display the user's name in the Sharp UI. Default is `name`.

## Login form

Sharp provides a login controller and view, which requires a session based guard. If you are in this case, you can use this default implementation and benefit from some classic features.

You can display a “Remember me” checkbox to the user:

```php
//in config/sharp.php

'auth' => [
    'suggest_remember_me' => true
]
```

You can leverage [rate limiting](https://laravel.com/docs/rate-limiting) to prevent brute force attacks:

```php
// in config/sharp.php

'auth' => [
    'rate_limiting' => [
        'enabled' => true,
        'max_attempts' => 5,
    ],
]
```

You can tweak this default form with a custom logo and an HTML message / section: see [related documentation here](style-visual-theme.md#login-and-menu-logos).

## Custom login form

You can entirely override the login workflow, view and controller, providing your custom endpoint:

```php
//in config/sharp.php

'auth' => [
    'login_page_url' => '/my_login',
]
```

## Custom guard

It's very likely that you don't want to authorize all users to access Sharp. You can hook into the [Laravel custom guards](https://laravel.com/docs/authentication#adding-custom-guards) functionality, with one config key:

```php
//in config/sharp.php

'auth' => [
    'guard' => 'sharp',
]
```

Of course, this implies that you defined a “sharp” guard in `config/auth.php`, as detailed in the Laravel documentation.

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

Finally, enable this feature by adding a config key:

```php
//in config/sharp.php

'auth' => [
    'check_handler' => \App\Sharp\Auth\SharpCheckHandler::class,
]
```

And you are good to go.
