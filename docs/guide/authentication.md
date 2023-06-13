# Authentication

Sharp won’t be used as a guest (at least in most cases). It leverages a default authentication system base on Laravel standards that you can configure to fit your needs. You can also entirely override the authentication workflow, as explained at the end of this page.

## Using the default authentication system

### Configure user attributes

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

### Login form

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

### Custom guard

It's very likely that you don't want to authorize all users to access Sharp. You can hook into the [Laravel custom guards](https://laravel.com/docs/authentication#adding-custom-guards) functionality, with one config key:

```php
//in config/sharp.php

'auth' => [
    'guard' => 'sharp',
]
```

Of course, this implies that you defined a “sharp” guard in `config/auth.php`, as detailed in the Laravel documentation.

### Authentication check

If you want a simple way to authorize some users to access Sharp in a project where you have other users, you can define an auth check rather than using custom guard.

First write a class which implements `Code16\Sharp\Auth\SharpAuthenticationCheckHandler`:

```php
class SharpCheckHandler implements SharpAuthenticationCheckHandler
{
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

### Two-factor authentication

Sharp provides a two-factor authentication (2fa) system, out of the box. You can enable it in the configuration:

```php
//in config/sharp.php

'auth' => [
    '2fa' => [
        'enabled' => true,
        'channel' => 'notification',  
    ],
]
```

With this configuration, Sharp will display a second screen to the user, after a successful password-based login, asking for a 6-digit code. This code will be provided to the user depending on the configured `channel`:
- `notification`: a notification is sent to the user (email by default, but you can tweak this, see below)
- `totp`: the user must use a 2fa authenticator app (like Google or Microsoft Authenticator — there are many options) to generate a code

### Handling the 2fa code via a notification

With this option, Sharp will send a notification to the user, containing the 6-digit code. By default, this notification is sent by email, but you can override this behavior by providing your own notification class:

```php
// in config/sharp.php

'auth' => [
    '2fa' => [
        'enabled' => true,
        'channel' => 'notification',
        'notification_class' => \App\Notifications\Sharp2faNotification::class,  
    ],
]
```

The notification must accept the code in the constructor, and display it in any way you want (email, SMS...):

```php
class Sharp2faNotification extends Notification
{
    public function __construct(public string $code) {}
    // ...
}
```

### Handling the 2fa code via a TOTP authenticator app

TBD


## Using a custom authentication workflow

You can entirely override the authentication workflow (view and controller) providing your custom endpoint:

```php
//in config/sharp.php

'auth' => [
    'login_page_url' => '/my_login',
]
```