# Authentication

Sharp won’t be used as a guest (at least in most cases). It leverages a default authentication system base on Laravel standards that you can configure to fit your needs. You can also entirely override the authentication workflow, as explained at the end of this page.

## Configure user attributes

The Sharp login form asks for a login and a password field; to handle the authentication, Sharp has to know what attributes it must test in your User model. Defaults are `email` and `password`, and can be overridden in the Sharp config:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setLoginAttributes('login', 'pwd')
            ->setUserDisplayAttribute('last_name')
            ->setUserAvatarAttribute('avatar_url')
            // [...]
    }
}
```

- The `setUserDisplayAttribute()` is useful to display the user's name in the Sharp UI. Default is `name`.
- The `setUserAvatarAttribute()` is useful to display the user's avatar in the Sharp UI. Default is `avatar`, when the attribute returns null, the default user icon is displayed instead.

## Login form

Sharp provides a login controller and view, which requires a session based guard. If you are in this case, you can use this default implementation and benefit from some classic features.

You can display a “Remember me” checkbox to the user, and leverage [rate limiting](https://laravel.com/docs/rate-limiting) to prevent brute force attacks:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->suggestRememberMeOnLoginForm()
            ->enableLoginRateLimiting(maxAttemps: 3)
            // [...]
    }
}
```

## Restrict access to Sharp to some users

It's very likely that you don't want to authorize all users to access Sharp. You can fix this in two ways:

### Global access gate

A simple way to restrict access to Sharp is to define the `viewSharp` global Gate, in the Service Provider:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    // [...]

    public function declareAccessGate(): void
    {
        Gate::define('viewSharp', function ($user) {
            return $user->is_sharp_admin; // Or any check you need
        });
    }
}
```

### Custom guard

You can also hook into the [Laravel custom guards](https://laravel.com/docs/authentication#adding-custom-guards) functionality, with this config:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setAuthCustomGuard('sharp')
            // [...]
    }
}
```

This implies that you defined a “sharp” guard in `config/auth.php`, as detailed [in the Laravel documentation](https://laravel.com/docs/authentication#adding-custom-guards).

## Two-factor authentication

Sharp provides a two-factor authentication (2fa) system, out of the box. You can configure it like this:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enable2faByNotification()
            // or ->enable2faByTotp()
            // or ->enable2faCustom()
            // [...]
    }
}
```

With this configuration, Sharp will display a second screen to the user, after a successful password based login, asking for a 6-digit code. This code will be provided to the user depending on the configuration:
- `enable2faByNotification()`: a notification is sent to the user (email by default, but you can tweak this, see below)
- `enable2faByTotp()`: the user must use a 2fa authenticator app (like Google or Microsoft Authenticator) to generate a code
- `enable2faCustom()`: in this case you must provide your own 2fa handler, see below.

### Handling the 2fa code via a notification

::: warning
To be able to receive notifications, your User model must use the `Illuminate\Notifications\Notifiable` trait.
:::

With this option, Sharp will send a notification to the user, containing the 6-digit code. By default, this notification is sent by email. You can override this behavior by providing your own handler class which must extend `Code16\Sharp\Auth\TwoFactor\Sharp2faNotificationHandler`:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enable2faCustom(\App\Sharp\My2faNotificationHandler::class)
            // [...]
    }
}
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

Then, you'll need to configure the totp handler:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enable2faByTotp()
            // [...]
    }
}
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

Finally, if you need more control, you can provide your own handler class via `->enable2faCustom()`, which must extend `Code16\Sharp\Auth\TwoFactor\Sharp2faTotpHandler`.

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

## Forgotten password

You can activate the classic Laravel Breeze workflow of forgotten password with a simple config:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enableForgottenPassword()
            // [...]
    }
}
```

This feature will imply by default that your User model implements a few interfaces, as detailed here: https://laravel.com/docs/passwords#model-preparation (and also refer to the [notification customization](https://laravel.com/docs/passwords#reset-email-customization) part of Laravel’s documentation).

And since Sharp was developed to allow various situations, you can tweak this feature depending on your actual implementation.
You can provide a custom reset password callback to decide how your user should be updated:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enableForgottenPassword(resetCallback: function ($user, $password) {
                $user->updatePasswordAfterReset($password);
            })
            // [...]
    }
}
```

Or alternatively, you can provide a full `Illuminate\Contracts\Auth\PasswordBroker` implementation, allowing you full control on how the reset should work:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enableForgottenPassword(broker: MyPasswordBroker::class)
            // [...]
    }
}
```

Finally, you can decide to hide the "reset password" link displayed in Sharp’s login form (in case you want to provide this functionality in another way, like a custom command in Sharp for instance):

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enableForgottenPassword(showResetLinkInLoginForm: false)
            // [...]
    }
}
```

These customizations will not interfere with any default behavior that you may have implemented for your app, outside Sharp.

## User impersonation (dev only)

At the development stage, it can be useful to replace the login form by a user impersonation. Sharp allows to do that out of the box:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enableImpersonation()
            // [...]
    }
}
```

::: warning
By default Sharp will also check the `APP_ENV` value to be `local` to enable this feature, since this should never hit the production by mistake.
You can override this behavior by providing a custom handler class, see below. 
:::

Configured like this, Sharp will display a dropdown list of all users in the login form, allowing you to select one and be logged in as this user. If you want more control on this users list, or if you need to opt out from this default Eloquent implementation, you can provide your own handler class, which must extend `Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler`:

```php
use \Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;

class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enableImpersonation(new class extends SharpImpersonationHandler {
                public function getUsers(): array
                {
                    return User::where('is_admin', true)
                        ->get()
                        ->filter(fn ($user) => $user->canImpersonate())
                        ->mapWithKeys(fn ($user) => [$user->id => $user->email])
                        ->all();
                }
            })
            // [...]
    }
}
```

## Use a custom authentication workflow

You can entirely override the authentication workflow (view and controller) providing your custom endpoint:

```php
use \Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;

class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->redirectLoginToUrl('/my_login')
            // [...]
    }
}
```
