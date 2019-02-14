# Sharp Context

Sharp provide a way to grab some request context values in the application code.

## Ask for context 

The class handling the context is `Code16\Sharp\Http\SharpContext`, a singleton. So at any point in the request, you can get it via:

```php
    app(Code16\Sharp\Http\SharpContext::class);
```

Sharp provides a very simple trait, `Code16\Sharp\Http\WithSharpContext`, which allow you to call `$this->context()` instead.

Here's a quick example:

```php
    class MyForm extends SharpForm
    {
        use WithSharpFormContext;
        
        function buildFormFields()
        {
            $this->addField(
                SharpFormTextField::make("name")
                    ->setReadOnly($this->context()->isUpdate());
            }
            [...]
        }
        [...]
    }
```

## What's in the context

For now, `SharpContext` allows to:

- (Form) know if we a on a creation or in an update case: `isUpdate()` and `isCreation()`
- (Form) grab the instance id (in an update case): `instanceId()`

These are useful in a Form situation to display or hide fields depending on the instance status (creation, update), or in a Validator to add an id exception in an unique rule. 

- get the value of a Global Filter, via `globalFilterFor($filterName)`: see the [Global Filter documentation](filters.md) to know more about this feature. 

---

> next chapter: [Sharp built-in solution for uploads](sharp-built-in-solution-for-uploads.md).