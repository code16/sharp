# Sharp Context

Sharp provide a way to grab some request context values in the application code.

## Generalities

The class handling the context is `Code16\Sharp\Http\Context\SharpContext`; at any point in the request, you can get it via the global helper:

```php
sharp()->context();
```

## Current context

Let's start with a simple example of how to use the context in a Form to set a field as read-only when the form is in update mode:

```php
class MyForm extends SharpForm
{
    // ...
    
    function buildFormFields()
    {
        $this
            ->addField(
                SharpFormTextField::make('key')
                    ->setReadOnly(sharp()->context()->isUpdate())
            )
            ->addFiled(/*...*/);
    }
}
```

The SharpContext class allows you to get the following information: 

### `entityKey(): string`

Grab the current entity key.

### `isEntityList(): bool`
### `isShow(): bool`
### `isForm(): bool`

Find out the current page type.

### `isUpdate(): bool`
### `isCreation(): bool`

In Form case, check the current status.

### `instanceId(): string`

In Form and Show Page cases, grab the instance id.

## Interact with Sharp's Breadcrumb

To interact with Sharp's breadcrumb, you can call:

```php
sharp()->context()->breadcrumb();
```

... and then use the following methods:

### `currentSegment(): BreadcrumbItem`
### `previousSegment(): BreadcrumbItem`

Get the current or previous breadcrumb item.

### `previousShowSegment(?string $entityKey = null): ?BreadcrumbItem`
### `previousListSegment(?string $entityKey = null): ?BreadcrumbItem`

Get (if existing) the closest Show or List in the breadcrumb.

### The `BreadcrumbItem` class

A `BreadcrumbItem` instance has the same methods seen above:

#### `entityKey(): string`
#### `isEntityList(): bool`
#### `isShow(): bool`
#### `isForm(): bool`
#### `isUpdate(): bool`
#### `isCreation(): bool`
#### `instanceId(): string`

Here's an example of how this information could be useful: imagine you have a Show for a `Post` instance, with an Embedded Entity List of `Comment`. When creating a new `Comment`, you'll need to set its `post_id` attribute on the Form `update()` method. You can for this make use of the breadcrumb context like this:

```php
class CommentForm extends SharpForm
{
    // ...
    
    function update($id, array $data)
    {
        $comment = $id 
            ? Comment::find($id) 
            : new Comment([
                'post_id' => sharp()->context()->breadcrumb()->previousShowSegment('post')->instanceId()
            ]);

        $this->save($comment, $data);
        
        return $comment->id;
    }
}
```

## Global and retained filters

### `globalFilterValue(string $handlerClassOrKey): array|string|null`

Get the value of a Global Filter: see the [Global Filter documentation](filters.md) to know more about this feature.

### `retainedFilterValue(string $handlerClassOrKey): array|string|null`

Get the value of a retained Filter: see the [Retained Filter documentation](filters.md) to know more about this feature.


## Access instances cached by an Entity List

This feature is really useful to avoid multiple queries on the same instance in a single request. The [specific documentation is available here](avoid-n1-queries-in-entity-lists.md).

