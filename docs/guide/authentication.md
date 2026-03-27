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
- The `setUserAvatarAttribute()` is useful to display the user's avatar in the Sharp UI. By default, a user icon is displayed instead.

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

## Forgotten password

You can activate the classic Laravel workflow of forgotten password with a simple config:

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

## Allow the current user to change his password

Sharp provides a helper trait to quickly build a command that lets the currently authenticated user change his password: `Code16\Sharp\Auth\Password\Command\IsChangePasswordCommandTrait`. Using this trait, you can quickly build a Sharp command, with a few configuration options.

The trait will take care of the form, validation and rate-limiting. Note that:

- This helper is designed for the “current user changes his own password” scenario. If you need admin-managed password resets for other users, implement a different command with the proper authorization checks.
- Persisting the new password is up to you (see example below).

### Configuration and behavior

You can configure the behavior of the command with the following methods (should be called in your `buildCommandConfig()` method):

- `configureConfirmPassword(?bool $confirm = true)`:  (false by default) enable password confirmation.
- `configurePasswordRule(Password $rule)`: (default: `Password::min(8)`) change the default password validation rule.
- `configureValidateCurrentPassword(?bool $validate = true)`: (true by default) if true, a `password` field that uses Laravel’s `current_password` rule (which compares against the currently authenticated user’s stored password) is added. Make sure you use Eloquent, and that your `User` model stores a hashed password as usual.

### Full example

```php
use Code16\Sharp\Auth\Password\Command\IsChangePasswordCommandTrait;
// ...

class ChangePasswordCommand extends SingleInstanceCommand
{
    use IsChangePasswordCommandTrait;

    public function buildCommandConfig(): void
    {
        $this->configureConfirmPassword()
            ->configurePasswordRule(
                Password::min(8)
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            );
    }

    protected function executeSingle(array $data): array
    {
        // The trait handles validation and rate limiting.
    
        auth()->user()->update([
            'password' => $data['new_password'], // Considering hashing is done by the model (cast)
        ]);

        $this->notify('Password updated!');

        return $this->reload();
    }
}
```

::: info
In this example we chose to create a `SingleInstanceCommand`, since it’s a common use-case to attach such a command to a "Profile" single Show Page that could be [placed in the user menu](building-menu.md#add-links-in-the-user-profile-menu), but you can decide to create an `EntityCommand` or even an `InstanceCommand` instead.
:::

## User impersonation (dev only)

At the development stage, it can be useful to replace the login form by a user impersonation. Sharp allows you to do that out of the box:

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
By default Sharp will also check the `APP_ENV` value to be `local` (or `testing`) to enable this feature, since this should never hit the production by mistake.
You can override this behavior by providing a custom handler class, see below. 
:::

Configured like this, Sharp will display a dropdown list of all users in the login form, allowing you to select one and be logged in as this user. If you want more control on this users list, or if you need to opt out from this default Eloquent implementation, you can provide either a Closure with must return a key-value array:

```php
use \Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;

class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enableImpersonation(fn () => User::query()
                ->where('is_admin', true)
                ->pluck('email', 'id')
                ->all()
            )
            
            // ...
    }
}
```

Or your own handler class, which must extend `Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler`:

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
                        ->pluck('email', 'id')
                        ->all();
                }
            })
            
            // ...
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
            ->redirectLogoutToUrl('/my_logout')
            // [...]
    }
}
```

## Two-factor authentication (2fa)

See [Two-factor authentication](authentication-2fa)

## Passkeys authentication

See [Passkeys authentication](authentication-passkeys)



