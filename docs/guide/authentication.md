# Authentication

Sharp won’t be used as a guest (at least in most cases). It leverages a default authentication system base on Laravel standards that you can configure to fit your needs. You can also entirely override the authentication workflow, as explained at the end of this page.

## Using the default authentication system

### Configure user attributes

The Sharp login form asks for a login and a password field; to handle the authentication, Sharp has to know what attributes it must test in your User model. Defaults are `email` and `password`, and can be overridden in the Sharp config:

```php
// config/sharp.php
return [
    // [...]
    
    'auth' => [
        'login_attribute' => 'login',
        'password_attribute' => 'pwd',
        'display_attribute' => 'name',
    ],
}
```

The third attribute, `display_attribute`, is used to display the user's name in the Sharp UI. Default is `name`.

### Login form

Sharp provides a login controller and view, which requires a session based guard. If you are in this case, you can use this default implementation and benefit from some classic features.

You can display a “Remember me” checkbox to the user:

```php
// config/sharp.php
return [
    // [...]

    'auth' => [
        'suggest_remember_me' => true
    ],
]
```

You can leverage [rate limiting](https://laravel.com/docs/rate-limiting) to prevent brute force attacks:

```php
// config/sharp.php
return [
    // [...]

    'auth' => [
        'rate_limiting' => [
            'enabled' => true,
            'max_attempts' => 5,
        ],
    ],
]
```

You can tweak this default form with a custom logo and an HTML message / section: see [related documentation here](style-visual-theme.md#login-and-menu-logos).

### Custom guard

It's very likely that you don't want to authorize all users to access Sharp. You can hook into the [Laravel custom guards](https://laravel.com/docs/authentication#adding-custom-guards) functionality, with one config key:

```php
// config/sharp.php
return [
    // [...]

    'auth' => [
        'guard' => 'sharp',
    ],
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
// config/sharp.php
return [
    // [...]

    'auth' => [
        'check_handler' => \App\Sharp\Auth\SharpCheckHandler::class,
    ],
]
```

And you are good to go.

### Two-factor authentication

Sharp provides a two-factor authentication (2fa) system, out of the box. You can enable it in the configuration:

```php
// config/sharp.php
return [
    // [...]

    'auth' => [
        '2fa' => [
            'enabled' => true,
            'handler' => 'notification',  
        ],
    ],
]
```

With this configuration, Sharp will display a second screen to the user, after a successful password based login, asking for a 6-digit code. This code will be provided to the user depending on the configured `handler`:
- `notification`: a notification is sent to the user (email by default, but you can tweak this, see below)
- `totp`: the user must use a 2fa authenticator app (like Google or Microsoft Authenticator) to generate a code
- a class name: you can provide your own 2fa handler, see below

### Handling the 2fa code via a notification

::: warning
To be able to receive notifications, your User model must use the `Illuminate\Notifications\Notifiable` trait.
:::

With this option, Sharp will send a notification to the user, containing the 6-digit code. By default, this notification is sent by email. You can override this behavior by providing your own handler class which must extend `Code16\Sharp\Auth\TwoFactor\Sharp2faNotificationHandler`:

```php
// config/sharp.php
return [
    // [...]

    'auth' => [
        '2fa' => [
            'enabled' => true,
            'handler' => \App\Sharp\My2faNotificationHandler::class,  
        ],
    ],
]
```

```php
class My2faNotificationHandler extends Sharp2faNotificationHandler
{
    protected function getNotification(int $code): Notification
    {
        return new My2faDefaultNotification($code);
    }
}
```

### Handling the 2fa code via a TOTP authenticator app

::: warning
This implies a bit more work to implement, but this method is more secure than the notification handler. The out-of-the-box implementation implies that you leverage Eloquent.
:::

With this option, Sharp will ask the user to register the app in a 2fa authenticator (like Google or Microsoft Authenticator). The user will have to provide a 6-digit code generated by the app to Sharp, in order to be authenticated.

First, require two packages needed for this feature:

```bash
composer require pragmarx/google2fa-laravel
composer require bacon/bacon-qr-code:"~2.0"
```

Then, you'll need to configure the totp handler in the Sharp config:

```php
// config/sharp.php
return [
    // [...]

    'auth' => [
        '2fa' => [
            'enabled' => true,
            'handler' => 'totp',  
        ],
    ],
]
```

Add three columns in the users table to store the 2fa secret, 2fa recovery codes and 2fa confirmation timestamp. Here’s a migration example:

```php
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_secret')
                ->after('password')
                ->nullable();

            $table->text('two_factor_recovery_codes')
                ->after('two_factor_secret')
                ->nullable();

            $table->timestamp('two_factor_confirmed_at')
                ->after('two_factor_recovery_codes')
                ->nullable();
        });
    }
};
```

After that, you must provide a way for your users to register the app in their 2fa authenticator. Sharp can help a lot with that, by extending two built-in Commands; one for activating and one for deactivating 2fa. The idea is to add these commands in a "profile" SingleShow, or in some related Entity List.

```php
class Activate2faCommand extends SingleInstanceWizardCommand
{
    use Code16\Sharp\Auth\TwoFactor\Commands\Activate2faViaTotpWizardCommandTrait;
}
```

```php
class Deactivate2faCommand extends SingleInstanceCommand
{
    use Code16\Sharp\Auth\TwoFactor\Commands\Deactivate2FaViaTotpSingleInstanceCommandTrait;
    // or Code16\Sharp\Auth\TwoFactor\Commands\Deactivate2FaViaTotpEntityCommandTrait
}
```

The first command is a wizard which will guide the user through the registration process; the second one is to deactivate the 2fa. Both require to enter a password.
You can tweak these commands and provide your own implementation if needed.

Finally, if you need more control, you can provide your own handler class, which must extend `Code16\Sharp\Auth\TwoFactor\Sharp2faTotpHandler`, and replace the `totp` handler in the configuration by its full class name.

### Enabling 2fa for some users only

Providing your own handler implementation, you can override the `isEnabledFor` method to enable 2fa for some users only:

```php
class My2faNotificationHandler extends Sharp2faNotificationHandler // or Sharp2faTotpHandler
{
    public function isEnabledFor($user): bool
    {
        return $user->hasGroup('sharp');
    }
}
```

### Customize the 2fa form

You can also change the default help text display above the 2fa form in the handler:

```php
class My2faNotificationHandler extends Sharp2faNotificationHandler // or Sharp2faTotpHandler
{
    public function formHelpText(): string
    {
        return sprintf(
            'You code was sent via SMS to your phone number ending in %s',
            substr(User::find($this->userId())->phone, -4)
        );
    }
}
```

## Using a custom authentication workflow

You can entirely override the authentication workflow (view and controller) providing your custom endpoint:

```php
// config/sharp.php
return [
    // [...]

    'auth' => [
        'login_page_url' => '/my_login',
    ],
]
```
