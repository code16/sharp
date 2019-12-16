---
sidebarDepth: 3
---

# Using SingleForm for unique resources

Sometimes you will need to configure a "unique" resource that does not fit into a List / Form schema, like for instance an account, or a configuration item. SingleForms are the natural companions for SingleShows, [documented here](single-show.md).


## Generator

```sh
php artisan sharp:make:single-form <class_name> [--model=<model_name>]
```


## Write the class

Instead of extending `SharpForm`, our SingleForm implementation should extend `Code16\Sharp\Form\SharpSingleForm`. We still have to implement `buildFormFields()` and `buildFormLayout()` to declare the fields presenting the instance, but other methods are a bit different. First, `find()` and `update()` don't need any `$instanceId` parameter:

- `findSingle(): array`
- `updateSingle(array $data)`

And then, since SingleForms will not accept `store` and `delete` actions, related methods are unavailable.


### Full example

Let's write a SingleForm for the current User, where he can update its name and email (using `WithSharpFormEloquentUpdater` here as this example uses Eloquent):

```php
class AccountSharpForm extends SharpSingleForm
{
    use WithSharpFormEloquentUpdater;

    function buildFormFields()
    {
        $this
            ->addField(
                SharpFormTextField::make("name")
                    ->setLabel("Name")
            )->addField(
                SharpFormTextField::make("email")
                    ->setLabel("Email address")
            );
    }

    function buildFormLayout()
    {
        $this->addColumn(6, function($column) {
            return $column
                ->withSingleField("name")
                ->withSingleField("email");
        });
    }

    protected function findSingle()
    {
        return $this->transform(
            User::findOrFail(auth()->id())
        );
    }

    protected function updateSingle(array $data)
    {
        $this->save(
            User::findOrFail(auth()->id()), 
            $data
        );
    }
}
```


## How to declare it?

Like said before, SingleForms will only work in pair with a SingleShow; please refer [to this documentation](single-show.md#linking-a-singleshow-to-the-main-menu) to find out how to declare a single show and form in the sharp config file.
