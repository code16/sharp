---
sidebarDepth: 3
---

# Building a Show Page

Between an Entity List and a Form, you might want to add a Show page to display a whole instance, and allow the user to interact with it through Commands.

Note that building a Show Page is really optional; but in some situations it could be really helpful to add this layer — and it can be even a must-have when dealing with "single" resources, such as a personal account, or a configuration entity, for which it's weird to build an Entity List.

## Generator

```bash
php artisan sharp:make:show-page <class_name> [--model=<model_name>]
```

## Write the class

First we build a class dedicated to our Show Page extending `Code16\Sharp\Show\SharpShow`; and we'll have to implement:

- `buildShowFields(FieldsContainer $showFields)` and `buildShowLayout(ShowLayout $showLayout)` to declare the fields presenting the instance.
- `find($id): array` to retrieve the instance.
- `delete($id): void` to delete the instance.
- `buildShowConfig()` (optional).

In detail:

### `buildShowFields(FieldsContainer $showFields): void`

Very much like Form's `buildFormFields()`, this method is meant to host the code responsible for the declaration and configuration of each show field. This must be done by calling `$showFields->addField`:

```php
function buildShowFields(FieldsContainer $showFields): void
{
    $showFields
        ->addField(
            SharpShowTextField::make('name')
                ->setLabel('Name')
        )
        ->addField(
            SharpShowPictureField::make('picture')
        );
}
```

#### Common attributes to all show fields

Each available Show field is detailed below; here are the attributes they all share:

- `setShowIfEmpty(bool $show = true): self`: by default, an empty field (meaning: with null or empty data) is not displayed at all in the Show UI. You can change this behaviour with this attribute.

#### Available simple Show fields

- [Text](show-fields/text.md)
- [Picture](show-fields/picture.md)
- [File](show-fields/file.md)
- [List](show-fields/list.md)

#### Embedding an Entity List in a Show

A crucial feature in the ability given to add a full Entity List in a Show, to display and interact with some "one to many" related data.

Let's review a simple example: we want to display the product list of an order. In the order Show, we can add a products Entity List as a field:

```php
function buildShowFields(FieldsContainer $showFields): void
{
    SharpShowEntityListField::make('products');
}
```

Sharp will consider this as a regular Entity List configured with the `products` entity key (this name can be overridden as a second argument), and will display it the Show as a field (see below for layout), with the full feature set of an Entity List: filters, commands, reorder, entity state, search...

Clicking a row in the EntityList can lead to a Form, or another Show Page (depending on the Entity configuration). Sharp will maintain a navigation breadcrumb to keep track of the user path.

Notice that you have three possibilities for the actual code of this Entity List:

- if you want to have a "products" entity in the main menu, you can reuse the same Entity List instance for the Show (and configure it to scope the data, as we'll discuss below),
- or you can configure a dedicated Entity with a specific Entity List, without declaring it in the main menu,
- or you can have both, making the orders Show version of the products Entity List extend the main one.

As always with Sharp, implementation is up to you.

The next thing to do is to scope the data of the embedded Entity List. In our case, we want to display and interact only with the products for this order... For this and more on personalization, refer to the detailed documentation of this field:

- [embedded EntityList](show-fields/embedded-entity-list.md)

### `buildShowLayout(ShowLayout $showLayout): void`

The show layout is a simplified version of the Form layout, and is made of sections which contains `columns` of `fields`.

#### Sections

A section is just a block of fields, packed in columns:

```php
function buildShowLayout(ShowLayout $showLayout): void
{
    $showLayout->addSection(
        'Description', 
        function(ShowLayoutSection $section) {
            ...
        }
    );
}
```

A section can be declared *collapsable*:

```php
function buildShowLayout(ShowLayout $showLayout): void
{
    $showLayout->addSection(
        'Description', 
        function(ShowLayoutSection $section) {
            $section->setCollapsable();
        }
    );
}
```

#### Columns and fields

Just like for Forms, a `ShowLayoutSection` is made of columns and fields. So completing the example above:

```php
function buildShowLayout(ShowLayout $showLayout): void
{
    $showLayout->addSection(
        'Description', 
        function(ShowLayoutSection $section) {
            $section->addColumn(
                9, 
                function(ShowLayoutColumn $column) {
                    $column->withSingleField('description');
                }
            );
        }
    );
}
```

A `ShowLayoutColumn`, very much like a `FormLayoutColumn`, can declare single field rows and multi fields rows. Report to the [Form layout documentation](building-form.md#buildformlayout) to find out how.

#### SharpShowListField's layout

Like `SharpFormListField` in Forms, a `SharpShowListField` must declare its item layout, in order to describe how fields are displayed, like in this example:

```php
function(ShowLayoutSection $section) {
    $section->addColumn(9, 
        function(ShowLayoutColumn $column) {
             $column->withSingleField('pictures', function(ShowLayoutColumn $listItem) {
                  // Notice that the list item layout is just a ShowLayoutColumn
                  $listItem
                      ->withSingleField('file')
                      ->withSingleField('legend');
             });
        }
    );
}
```

#### Embedded Entity Lists

An embedded Entity List in treated as a special section; its label will be displayed as section title. 

```php
function buildShowLayout(ShowLayout $showLayout): void
{
    $showLayout->addEntityListSection('members');
}
```

Like regular sections, embedded Entity List can be declared *collapsable*.

### `find($id): array`

As for Forms, the method must return a key-value array:

```php
function find($id): array
{
    return [
        'name' => 'USS Enterprise',
        'capacity' => 3000
    ];
}
```

And as for Forms, you'll want to transform your data before sending it. 

```php
function find($id): array
{
	return $this
		->setCustomTransformer(
		    'name', 
		    fn ($value, $product) => strtoupper($product->name)
		)
		->setCustomTransformer(
		    'picture', 
		    new SharpUploadModelThumbnailUrlTransformer(600)
		);
}
```

Transformers are explained in the detailed [How to transform data](how-to-transform-data.md) documentation.

### `delete($id): void`

Here you might write the code performed on a deletion of the instance. It can be anything, here's an Eloquent example:

```php
function delete($id): void
{
    Product::findOrFail($id)->delete();
}
```

### `buildShowConfig(): void`

Very much like EntityLists, a Show can declare a config with `EntityState` handler, or Breadcrumb configuration; you can also define here an attribute that will be used as page title.

```php
function buildShowConfig()
{
   $this
        ->configureBreadcrumbCustomLabelAttribute('name')
        ->configurePageTitleAttribute('title')
        ->configureEntityState('state', OrderEntityState::class);
}
```

Here is the full list of available methods:

- `configureBreadcrumbCustomLabelAttribute(string $breadcrumbAttribute)`: declare the data attribute to use for the breadcrumb; [see detailed doc](sharp-breadcrumb.md)
- `configurePageAlert(string $template, string $alertLevel = null, string $fieldKey = null, bool $declareTemplateAsPath = false)`: display a dynamic message above the Show Page; [see detailed doc](page-alerts.md)
- `configureEntityState(string $stateAttribute, $stateHandlerOrClassName)`: add a state
  toggle, [see detailed doc](entity-states.md)
- `configurePageTitleAttribute(string $titleAttribute, bool $localized = false)`: define a title to the Show Page, configuring an attribute that should be part of the `find($id)` array
- `configureDeleteConfirmationText(string $text)` to add a custom confirm message when the use clicks on the delete button.

## Accessing the navigation breadcrumb

A common pattern for Shows is to add an embedded EntityList with related entities, and to allow update but also creation from there. Taking back our order / products example, we may need to add a product to the order. Question is: how can we attach a newly created product to an existing order?

The answer is by accessing the navigation breadcrumb, with [Sharp Context](context.md), and more precisely with its `getPreviousPageFromBreadcrumb()` method. Here's a full example:

```php
class ProductSharpForm extends SharpForm
{
    function update($id, array $data)
    {
        $product = $id ? Product::findOrFail($id) : new Product;
        $product = $this->save($product, $data);
        
        if(currentSharpRequest()->isCreation()) {
              Order::findOrFail(currentSharpRequest()->getPreviousShowFromBreadcrumbItems()->instanceId())
                  ->products()
                  ->attach($product->id);
        }
    }
}
```

## Declare the Show Page

The show Page must be declared in the correct entity class, as documented here: [Write an entity](entity-class.md).
