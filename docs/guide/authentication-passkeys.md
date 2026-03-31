# Passkeys

Sharp provides a built-in solution to manage and authenticate with passkeys. Passkeys are a replacement for passwords that provide faster, easier, and more secure sign-ins to websites and apps across a user’s devices.

## Installation

Passkeys in Sharp requires the `spatie/laravel-passkeys` package.
Follow the [installation instructions](https://spatie.be/docs/laravel-passkeys/installation-setup) of the package **(the JavaScript installation part is not needed for Sharp)**.

## Configuration

To enable passkeys in Sharp, use the `enablePasskeys()` method in your `SharpServiceProvider`:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enablePasskeys()
            // [...]
    }
}
```

By default, Sharp will prompt the user to create a passkey after a successful password-based login if they don't have one yet. You can disable this feature like this :

```php
$config->enablePasskeys(promptAfterLogin: false);
```

## Management in the User Profile

Once enabled, Sharp automatically registers a `PasskeyEntity` that you can use in your application. A common use case is to allow users to manage their passkeys from their profile page using a `SharpShowEntityListField`.

Here is an example of how to add the passkey management list to a `ProfileSingleShow`:

```php
use Code16\Sharp\Auth\Passkeys\Entity\PasskeyEntity;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
// ...

class ProfileSingleShow extends SharpSingleShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields
            // [...]
            ->addField(
                SharpShowEntityListField::make(PasskeyEntity::class)
                    ->setLabel('Passkeys')
            );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection('', function (ShowLayoutSection $section) {
                // [...]
            })
            ->addEntityListSection(PasskeyEntity::class);
    }
    
    // ...
}
```
