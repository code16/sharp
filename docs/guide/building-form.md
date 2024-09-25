---
sidebarDepth: 3
---

# Building a Form

Forms as used to create or update instances.

## Generator

```bash
php artisan sharp:make:form <class_name> [--model=<model_name>,--single]
```

## Write the class

As usual in Sharp, we begin by creating a class dedicated to our Form and make it extend `Code16\Sharp\Form\SharpForm`; and we'll have to implement at least 4 functions:

- `buildFormFields(FieldsContainer $formFields)` to declare fields, 
- `buildFormLayout(FormLayout $formLayout)` to handle fields layout,
- `find($id): array` to get the instance data,
- `update($id, array $data)` to update the instance.

Let's see the specifics:

### `buildFormFields(FieldsContainer $formFields)`

In short, this method is meant to host the code responsible for the declaration and configuration of each form field.
This must be done calling `$formFields->addField`:

```php
class ProductForm extends SharpForm
{
    // ...
    
	public function buildFormFields(FieldsContainer $formFields): void
	{
		$formFields
			->addField(
				SharpFormTextField::make('name')
					->setLabel('Name')
			)
			->addField(
				SharpFormTextField::make('capacity')
					->setLabel('Full capacity (x1000)')
			);
	}
}
```

As we can see in this simple example, we defined two text fields giving them a mandatory `key` and an optional label.

#### Form fields shared attributes

Every field has the optional following setters:

- `setLabel(string $label)` for the field label displayed above it
- `setHelpMessage(string $helpMessage)` to add a help text below the field
- `setReadOnly(bool $readOnly = true)`
- `setExtraStyle(string $style)`: the CSS style will be added in a `style` attribute

In addition, all text fields have one more generic setter:

- `setPlaceholder(string $placeholder)`

#### Conditional display

The idea is to hide or show a field (referred as "secondary") depending on some other field (referred as "main") value. To do that, use the `addConditionalDisplay(string $fieldKey, $values = true)` setter giving:

- the main `$fieldKey`, which should refer to either a Check, Select, Tags or Autocomplete field,

- the `$values` of the main field for which the secondary field must be visible. You can put there a boolean for a Check master field, and for other fields (Select, Tags, Autocomplete), either:
	- a string value, like for instance `'red'`: the slave field is visible only when the main field value is "red"
	- a string value with a negation mark as the first char, like `'!red'`: the secondary field is visible only when the main field value is NOT "red"
	- an array of values: `['red', 'blue']`. The secondary field is visible only when the main field value is either "red" or "blue".

You can add multiple conditional display rules, chaining calls to `addConditionalDisplay(string $fieldKey, $values = true)`. In this case, all conditions will be linked with a `AND` operator by default (meaning all conditions must be verified to display the secondary field), but this can be switched to an `OR` easily with `setConditionalDisplayOrOperator()` (and back with `setConditionalDisplayAndOperator()`).

#### Formatters

Every field is linked to a Formatter, which defines the way data is formatted right before sending it to the front (last step, after transformers) and right after reception from the front (first step, before transformers).

Sharp provides a Formatter implementation per field type, but you can override this using the `setFormatter($formatter)` setter, providing a `Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter` implementation.

#### Form fields specific attributes

For the specifics of each field, here's the full list and documentation:

- [Text](form-fields/text.md)
- [Textarea](form-fields/textarea.md)
- [Editor (rich text rendered as Markdown or HTML)](form-fields/editor.md)
- [Number](form-fields/number.md)
- [Html](form-fields/html.md)
- [Check](form-fields/check.md)
- [Date](form-fields/date.md)
- [Upload](form-fields/upload.md)
- [Select](form-fields/select.md)
- [Autocomplete](form-fields/autocomplete.md)
- [Tags](form-fields/tags.md)
- [List](form-fields/list.md)
- [AutocompleteList](form-fields/autocomplete-list.md)
- [Geolocation](form-fields/geolocation.md)

### `buildFormLayout(FormLayout $formLayout)`

Now let's build the form layout. A form layout is made of `columns`, which contains `fields`, `lists` of fields and `fieldsets`. If needed, we can even define `tabs` above `columns`.

#### Columns and fields

Here's how we can define the layout for the simple two-fields form we built above:

```php
class ProductForm extends SharpForm
{
    // ...
    
	public function buildFormLayout(FormLayout $formLayout): void
	{
		$formLayout->addColumn(6, function (FormLayoutColumn $column) {
			$column->withField('name')
				->withField('capacity');
		});
	}
}
```

This will result in a 50% column (columns width are 12-based, like in Entity Lists) with the 2 fields in separate rows. Note that fields are referenced with their key, previously defined in `buildFormFields()`.

Here's another possible layout, with two unequally large columns:

```php
class ProductForm extends SharpForm
{
    // ...
    
	public function buildFormLayout(FormLayout $formLayout): void
	{
		$formLayout
			->addColumn(7, function (FormLayoutColumn $column) {
				$column->withField('name');
			})
			->addColumn(5, function (FormLayoutColumn $column) {
				$column->withField('capacity');
			});
	}
}
```

##### Displaying fields on the same row

One final way is to put fields side by side on the same column:

```php
class ProductForm extends SharpForm
{
    // ...
    
	public function buildFormLayout(FormLayout $formLayout): void
	{
		$formLayout->addColumn(6, function (FormLayoutColumn $column) {
			$column->withFields('name', 'capacity');
		});
	}
}
```

This will align the two fields on the row. They'll have the same width (50%), but we can act on this adding a special suffix:

```php
$column->withFields('name|8', 'capacity|4');
```

Once again, it's a 12-based grid, so `name` will take 2/3 of the width, and `capacity` 1/3.

##### A word on small screens

Columns are only used in medium to large screens (768 pixels and up).

Same for fields put on the same row: on smaller screens, they'll be placed on different rows, except if another layout is intentionally configured, using this convention:

```php
$column->withFields('name|8,6', 'capacity|4,6');
```

Here, `name` will take 8/12 of the width on large screens, and 6/12 on smaller one.


#### Fieldsets

Fieldsets are useful to group some fields in a labelled block. Here's how they work:

```php
$formLayout->addColumn(6, function (FormLayoutColumn $column) {
    $column->withFieldset('Details', function (FormLayoutFieldset $fieldset) {
        return $fieldset
            ->withField('name')
            ->withField('capacity');
    });
});
```

"Details" is here the legend of the fieldset.

#### Lists of fields

In a `List` case, which is a form fields container [documented here](form-fields/list.md), we have to describe the list item layout, using `->withListField()` and passing a Closure as second argument:

```php
$column->withListField('pictures', function (FormLayoutColumn $listItem) {
    $listItem
        ->withField('file')
        ->withField('legend');
});
```

#### Conditions

Since layout classes apply Laravel’s `Conditionable` trait, you can use the `when()` method to conditionally display a column:

```php
$column
	->withField('title')
	->when(sharp()->context()->isUpdate(), function (FormLayoutColumn $column) {
		$column->withField('author');
	});
```

#### Tabs

Finally, columns can be wrapped in tabs if the form needs to be in parts:

```php
$formLayout
    ->addTab('tab 1', function (FormLayoutTab $tab) {
        $tab->addColumn(6, function (FormLayoutColumn $column) {
            $column->withField('name');
            // ...
	    });
    })
    ->addTab([...])
```

The tab will here be labelled "tab 1".

### `find($id): array`

Next, we have to write the code responsible for the instance data (in an update case). The method must return a key-value array:

```php
class ProductForm extends SharpForm
{
    // ...
    
	public function find($id): array
	{
		return [
			'name' => 'USS Enterprise',
			'capacity' => 3000
		];
	}
}
```

As for the Entity List, you'll want to transform your data before sending it. Transformers are explained in the detailed [How to transform data](how-to-transform-data.md) documentation.


### `update($id, array $data)`

Well, this is the core: how to write the actual update code.

#### Form field format

Before going into the details, please note that the `$data` array contains the per-field formatted data: depending on the type of SharpFormField you used, the structure may change.

For instance, a `SharpFormEditorField` content will be formatted as an array with a `text` attribute for the full text and an optional `fields` attribute with embedded fields (see the Editor field documentation for more details).

Sharp will use this format step to perform some tasks: move or copy uploaded files, handle image transformation, ... Note that you can override the formatter of a specific field as explained above in the `buildFormFields()` section.

Now let's review two cases:

#### General case: you are on your own

If you are not using Eloquent (and maybe no database at all), you'll have to do it manually.

Remember: Sharp aims to be as permissive as possible. So just write the code to update the instance designated by `$id` with the values in the formatted `$data` array.

#### Eloquent case (where the magic happens)

Sharp also aims to help the applicative code to be as small as possible, and if you're using Eloquent, you can import a dedicated trait: `Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater`. And then, write this kind of code:

```php
class ProductForm extends SharpForm
{
    // ...
    
    public function update($id, array $data)
    {
        $instance = $id ? Product::findOrFail($id) : new Product;
    
        $this
            ->setCustomTransformer('price', fn ($price) => $price / 100)
            ->ignore('comment')
            ->save($instance, $data);
    }
}
```

We first define a custom transformer (see [detailed documentation](how-to-transform-data.md)).

Then we decide for some reason to bypass the automatic save process for the `comment` attribute. This `ignore()` function can be called with an array as well. You'll probably do whatever is necessary for this field after the `save()` call.

Finally, we call `$this->save()` with the instance and the sent data. This method will do all the persisting code for you, handling if needed related models (for lists, tags, selects, ...), with any relation allowed by Eloquent (hasMany, belongsToMany, morphMany, ...).

#### Handle applicative exceptions

In the `update($id, array $data)` method you may want to throw an exception on a special case, other than validation (which is explained below). Here's how to do that:

```php
class ProductForm extends SharpForm
{
    // ...
	public function update($id, array $data)
	{
		// ...
	
		if($sometingIsWrong) {
			throw new SharpApplicativeException('Something is wrong');
		}
		
		// ...
	}
}
```

The message will be displayed to the user.

#### Return the instance id

This method must return the id of the updated or stored instance.

#### Display notifications

Sometimes you'll want to display a message to the user, after a creation or an update. Sharp way to do this is to call `->notify()` in the Form code:

```php
class ProductForm extends SharpForm
{
    // ...

	public function update($id, array $data)
	{
		$instance = $id ? Product::findOrFail($id) : new Product;
	
		$this->save($instance, $data);
	
		$this->notify('Product was indeed updated.')
			 ->setDetail('As you asked.')
			 ->setLevelSuccess()
			 ->setAutoHide(false);
	
		return $instance->id;
	}
}
```

A notification is made of a title, and optionally
- a text detail,
- a notification level: info (the default), warning, danger, success,
- an auto-hide policy (if true, the toasted notification will hide after 4s).

The notification will be displayed on the next screen, which is the Entity List.

Note that you can add up notifications, calling the `notify()` function multiple times (which is useful to sometimes add a second notification, based on actual form data).

### `create(): array`

This method **is not mandatory**, a default implementation is proposed by Sharp, but you can override it if necessary. The aim is to return an array version of a new instance (for the creation form). For instance, with Eloquent and the `Code16\Sharp\Utils\Transformers\SharpAttributeTransformer` trait:

```php
class ProductForm extends SharpForm
{
    // ...
    
	public function create(): array
	{
		return $this->transform(new Product(['name' => 'new']));
	}
}
```

### `buildFormConfig(): void`

This method, entirely optional, is the place to configure these:

- `configureBreadcrumbCustomLabelAttribute(string $attribute)` to declare the attribute used by the breadcrumb (see [breadcrumb documentation](sharp-breadcrumb.md)).

- `configureDisplayShowPageAfterCreation(bool $displayShowPage = true)` to tell Sharp to redirect to the entity Show Page (instead of the EntityList) after the store. No existence check is done here, meaning if there is no Show Page configured it will end up in a 404.

- `configurePageAlert(string $template, string $alertLevel = null, string $fieldKey = null, bool $declareTemplateAsPath = false)`: display a dynamic message above the Form; [see detailed doc](page-alerts.md)

Example

```php
class ProductForm extends SharpForm
{
    // ...
    
	public function buildFormConfig(): void
	{
		$this->configureBreadcrumbCustomLabelAttribute('name')
			->setDisplayShowPageAfterCreation();
	}
}
```

## Input validation

In order to have an input validation on your form, you can either declare a `rules()` methode (and an optional `messages()` one):

```php
class ProductForm extends SharpForm
{
	// ...
    
    public function rules(): array
    {
    	return [
    		'name' => 'required',
			'price' => ['required', 'numeric'],
		];
    }
}
```

Or you can manually call `->validate()` in the `update()` method:

```php
class ProductForm extends SharpForm
{
	// ...
    
    public function update($id, array $data)
    {
    	$this->validate($data, [
    		'name' => 'required',
			'price' => ['required', 'numeric'],
		]);
    }
}
```

Sharp will handle the error display in the form.

## Declare the form

The Form must be declared in the correct entity class, as documented here: [Write an entity](entity-class.md)).
