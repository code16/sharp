---
sidebarDepth: 3
---

# Using Single Form for unique resources

Sometimes you will need to configure a "unique" resource that does not fit into a List / Form schema, like for example an account, or a configuration item. Single Forms are the natural companions for
Single Shows, [documented here](single-show.md).

## Write the class

Instead of extending `SharpForm`, our SingleForm implementation should extend `Code16\Sharp\Form\SharpSingleForm`. We still have to implement `buildFormFields(FieldsContainer $formFields)` and `buildFormLayout(FormLayout $formLayout)` to declare the fields presenting the instance, but other methods are a bit different. First, `find()` and `update()` don't need any `$instanceId` parameter:

- `findSingle(): array`
- `updateSingle(array $data)`

### Full example

Let's write a Single Form for the current User, where he can update its name and email (using `WithSharpFormEloquentUpdater` here as this example uses Eloquent):

```php
class AccountSharpForm extends SharpSingleForm
{
    use WithSharpFormEloquentUpdater;

    function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('name')
                    ->setLabel('Name')
            )
            ->addField(
                SharpFormTextField::make('email')
                    ->setLabel('Email address')
            );
    }

    function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout->addColumn(6, function($column) {
            return $column
                ->withSingleField('name')
                ->withSingleField('email');
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

Like said before, Single Forms will only work in pair with a Single Show; please refer [to this documentation](single-show.md#single-show-declaration) to find out how to declare a single show and form.
