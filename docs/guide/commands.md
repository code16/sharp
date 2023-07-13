# Write a Command

Commands in Sharp are a powerful way to integrate functional processes in the content management. They can be used to re-send an order to the customer, to synchronize pictures of a product, or to preview a page, for instance.

Commands can be defined in an Entity List, in a Show Page or in a Dashboard. This documentation will focus on the Entity List, but the API is very similar in all three cases as explained at the end of this page.

## Generator for an 'Entity' command

```bash
php artisan sharp:make:entity-command <class_name> [--with-form]
```

## Generator for an 'Instance' command

```bash
php artisan sharp:make:instance-command <class_name> [--with-form]
```

## Write the Command class

First we need to write a class for our Command. It must extend the `Code16\Sharp\EntityList\Commands\EntityCommand` abstract class (for "entity commands", more on that below), and implement two functions.

First one is `label(): string`, and must return the text label of the Command, displayed to the user:

```php
public function label(): string
{
    return 'Reload full list';
}
```

The second one, `execute(array $data=[]): array` handles the work of the Command itself:

```php
public function execute(array $data=[]): array
{
    return $this->reload();
}
```

### Command scope: instance or entity

The example above is an "entity" case, which is reserved to Entity Lists: Command applies to a subset of instances, or all of them. To get the Entity List context (search, page, filters...), you can check `$this->queryParams`, just like in the Entity List itself.

To create an instance Command (relative to a specific instance, which can be placed on each Entity List row, or in a Show Page), the Command class must extend `Code16\Sharp\EntityList\Commands\InstanceCommand`. The execute method signature is a bit different:

```php
public function execute($instanceId, array $params = []): array
{
    // [...]
}
```

Here we get an `$instanceId` parameter to identify the exact instance involved. The rest is the same, except for authorization detailed below.

### Add a Command form

The second parameter in the `execute()` function is an array named `$data`, which contains values entered by the user in a Command specific form. A use case might be to allow the user to enter a text to be sent to the customer with his invoice. In order to do that, we have first to write a `buildFormFields()` function in the Command class:

```php
function buildFormFields(FieldsContainer $formFields): void
{
    $formFields
        ->addField(
            SharpFormTextareaField::make('message')
                ->setLabel('Message')
        )
        ->addField(
            SharpFormCheckField::make('now', 'Send right now?')
                ->setHelpMessage('Otherwise it will be sent next night.')
        );
}
```

The API is the same as building a standard Form (see [Building an Entity Form](building-form.md)).

Once this method has been declared, a form will be prompted to the user in a modal as he clicks on the Command. Then, in the `execute()` method, you can grab the entered value, and even to handle the validation:

```php
public function execute($instanceId, array $data= []): array
{
    $this->validate($data, [
        'message' => 'required'
    ]);

    $text = $data['message'];
    // [...]
}
```

#### Initializing form data

You may need to display the form filled with some data; in order to do that, you have to implement the `initialData()` method:

```php
protected function initialData(): array
{
    return [
        'message' => 'Some initial value'
    ];
}
```

For an Instance command, add the `$instanceId` as a parameter:

```php
 protected function initialData($instanceId): array
 {
     // [...]
 }
```

This method must return an array of formatted values, like for a regular [Entity Form](building-form.md). This means you can [transform data](how-to-transform-data.md) here:

```php
protected function initialData($instanceId): array
{
    return $this
        ->setCustomTransformer('message', function($value, Order $instance) {
            return sprintf('Message #%s:', $instance->messages_sent_count);
        })
        ->transform(Order::find($instanceId));
}
```

Note that in both cases (Entity or Instance Command) you can access to the Entity List querystring via the request.

### Configure the command (confirmation text, description, form modal title...)

You can tweak this in an optional `buildCommandConfig()` function:

```php
public function buildCommandConfig(): void
{
    $this->configureConfirmationText('Sure, really?')
        ->configureDescription('This action will send a text message to your boss')
        ->configureFormModalTitle('Text message')
        ->configureFormModalButtonLabel('Execute');
}
```

Here is the full list of available methods:

- `configureConfirmationText(string $confirmationText)`: if set the Command will ask a confirmation to the user before executing
- `configureDescription(string $description)`: this text will appear under the Command label
- `configureFormModalTitle(string $formModalTitle)`: if the Command has a Form, the title of the modal will be its label, or `$formModalTitle` if defined
- `configureFormModalButtonLabel(string $formModalButtonLabel)`: if the Command has a Form, the label of the OK button will be `$formModalButtonLabel`
- `configurePageAlert(string $template, string $alertLevel = null, string $fieldKey = null, bool $declareTemplateAsPath = false)`: display a dynamic message above the Form; [see detailed doc](page-alerts.md)

### Command return types

  Finally, let's review the return possibilities: after a Command has been executed, the code must return something to tell to the front what to do next. There are six of them:

- `return $this->info('some text')`: displays the entered text in a modal.
- `return $this->reload()`: reload the current page (with context).
- `return $this->refresh(1)`*: refresh only the instance with an id on `1`. We can pass an id array also to refresh more than one instance.
- `return $this->view('view.name', ['some'=>'params'])`: display a view right in Sharp; useful for page previews.
- `return $this->link('/path/to/redirect')`: redirect to the given path.
- `return $this->download('path', 'diskName')`: the browser will download the specified file.
- `return $this->streamDownload('path', 'name')`: the browser will stream the specified file.

\* `refresh()` is only useful in an Entity List case (in a Dashboard or a Show Page, it will be treated as a `reload()`). In order to make it work properly, you have to slightly adapt the `getListData()` of your Entity List implementation, making use of `$this->queryParams->specificIds()`:

```php
function getListData()
{
    $orders = Order::query();

    if($params->specificIds()) {
        $orders->whereIn('id', $this->queryParams->specificIds());
    }

    return $this->transform($orders->get());
}
```

### Display notifications

In the same fashion as for a Form, you can display notifications after a Command has been executed. Here is an example:

```php
public function execute($instanceId, array $data= []): array
{
    // [...]

    $this->notify('This is done.')
         ->setDetail('As you asked.')
         ->setLevelSuccess()
         ->setAutoHide(false);

    return $this->reload();
}
```

See [form documentation](building-form.md#display-notifications) to learn more about the `notify()` method.

::: warning
Ensure to only use `notify()` in a Command that returns `reload()` or `refresh()`, otherwise the notification be delayed to the next browser reload.
:::

## Declare the Command

Once the Command class is written, we must add it to the Entity List or Show Page:

```php
function getInstanceCommands(): ?array
{
    return [
        OrderSendMessage::class
    ];
}

function getEntityCommands(): ?array // Not available in a Show Page
{
    return [
        OrderReload::class
    ];
}
```

or to the Dashboard:

```php
function getDashboardCommands(): ?array
{
    return [
        DashboardDownloadCommand::class
    ];
}
```

For the command itself, you can type a class name (as show in these examples), a class instance or a Closure.

You can optionally specify a command key. Sharp will use the command class name if it is missing as a default behavior, which should be ok in most cases.

```php
function getInstanceCommands(): ?array
{
    return [
        'message' => OrderSendMessage::class
    ];
}
```

## Handle authorizations

It's often mandatory to add authorizations to a Command. Here's how to do that:

### Authorizations for entity Commands

Implement the `authorize(): bool` function, which must return a boolean to allow or disallow the Command execution:

```php
public function authorize(): bool
{
    return auth()->user()->hasGroup('boss');
}
```

### Authorizations for instance Commands

For instance Commands we have to know the instance involved, which means the signature is different:

```php
public function authorizeFor($instanceId): bool
{
    return Order::find($instanceId)->owner_id == auth()->id();
}
```

### Define an entity Command as primary

An Entity List can declare one (and only one) of its entity Commands as "primary". In this case, the command will appear at the top, next to the creation button ("New..."). The idea is to provide more visibility to an important Command, but could also be to replace the creation button entirely (you need to remove the "create" authorization to achieve this). 

```php
function buildListConfig(): void
{
    $this->configurePrimaryEntityCommand(InviteNewUser::class);
}
```

A use case could be to provide a Command with a form for the "create" task, leaving the real Form only for update.

## Commands for Show Page

Show Pages can only define instance commands (obviously); apart from that, the API is the same.

It's a common pattern to reuse the same instance commands in an Entity List and in a Show Page.
One small difference is that `reload()` action is treated as a `refresh()`.

### Attach Commands to sections

One small difference between Commands in Entity List and in Show Page is that in the latter case it's possible to move the Command to a specific section (of the Show Page layout).

To achieve this, you must choose a unique key and attach it to the layout section, and use this key on instance commands declaration:

```php
// In a SharpShow

protected function buildShowLayout(ShowLayout $showLayout): void
{
    $showLayout
        ->addSection('General', function (ShowLayoutSection $section) {
            // ...
        })
        ->addSection('Content', function (ShowLayoutSection $section) {
            $section
                ->setKey('content-section') // <- The key could be anything
                ->addColumn(8, function (ShowLayoutColumn $column) {
                    // ...
                });
        });
}

public function getInstanceCommands(): ?array
{
    return [
        'content-section' => [  // <- Use the same key here
            PreviewPostCommand::class,
        ],
        EvaluateDraftPostWizardCommand::class,
    ];
}
```

With that, the `PreviewPostCommand` will appear alongside the "Content" section.

## Commands for Dashboard

Dashboard can use Commands too, with a very similar API, apart for:

- There is no Instance or Entity distinction; a command handler must extend `Code16\Sharp\Dashboard\Commands\DashboardCommand`.
- A Dashboard Command can not return a `refresh()` action, since there is no Instance.

## Bulk Commands (Entity List only)

As seen before, Entity Commands are executed on multiple instances: all of them, or a filtered list of them. But sometimes you may need to execute a Command on a specific list of instances, a selection.

In order to allow that, you must:

### Configure the Command to allow an instance selection

```php
public function buildCommandConfig(): void
{
    $this->configureInstanceSelectionAllowed();
}
```

You may use `configureInstanceSelectionRequired()` instead to declare that the command can not be executed without a selection.

### Apply the Command to the selected instances

You can use the `$this->selectedIds()` method to retrieve the list of selected instances ids, and apply the Command to them:

```php
public function execute(array $data = []): array
{
    Post::whereIn('id', $this->selectedIds())
        ->get()
        ->each(fn (Post $post) => $post->update(/* ... */));

    return $this->refresh($this->selectedIds());
}
```

## Wizard Commands

A Wizard Command is a special kind of Command that will be executed in a modal, and will be able to display several steps to the user. See [dedicated documentation here](commands-wizard.md).