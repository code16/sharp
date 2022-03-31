# Write a Command

Commands in Sharp are a powerful way to integrate functional processes in the content management. They can be used for instance to re-send an order to the customer, on synchronize pictures of a product, or preview a page...

Commands can be defined in an EntityList, in a Show Page or in a Dashboard. This documentation will take the EntityList case, but the API is very similar in all cases, as explained at the end of this page.

## Generator for an 'Entity' command

```bash
php artisan sharp:make:entity-command <class_name>
```

## Generator for an 'Instance' command

```bash
php artisan sharp:make:instance-command <class_name>
```


## Write the Command class

First we need to write a class for our Command. It must extend the `Code16\Sharp\EntityList\Commands\EntityCommand` abstract class (for "entity commands", more on that below), and implement two functions.

First one is `label(): string`, and must return the text label of the Command, displayed to the user:

```php
public function label(): string
{
    return "Reload full list";
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

The example above is an "entity" case, reserved to EntityLists: Command applies to a subset of instances, or all of
them. To get the EntityList context (search, page, filters...), you can check `$this->queryParams`, just like in the
EntityList itself.

To create an instance Command (relative to a specific instance, which can be placed on each EntityList row, or in a Sho
Page), the Command class must extend `Code16\Sharp\EntityList\Commands\InstanceCommand`. The execute method signature is
a bit different:

```php
public function execute($instanceId, array $params = []): array
{
    [...]
}
```

Here we get an `$instanceId` parameter to identify the exact instance involved. The rest is the same, except for authorization detailed below.


### Add a Command form

The second parameter in the `execute()` function is an array named `$data`, which contains values entered by the user in a Command specific form. A use case might be to allow the user to enter a text to be sent to the customer with his invoice. In order to do that, we have first to write a `buildFormFields()` function in the Command class:

```php
function buildFormFields(FieldsContainer $formFields)
{
    $formFields
        ->addField(
            SharpFormTextareaField::make("message")
                ->setLabel("Message")
        )
        ->addField(
            SharpFormCheckField::make("now", "Send right now?")
                ->setHelpMessage("Otherwise it will be sent next night.")
        );
}
```

The API is the same as building a standard Form (see [Building an Entity Form](building-form.md)).

Once this method has been declared, a form will be prompted in a modal to the user as he clicks on the Command. The
optional `public function formModalTitle(): string` method may return the custom title of this modal, if needed.

Then, in the `execute()` method, you can grab the entered value, and even to handle the validation:

```php
public function execute($instanceId, array $data= []): array
{
    $this->validate($data, [
        "message" => "required"
    ]);

    $text = $data["message"];
    [...]
}
```

#### Initializing form data

You may need to initialize the form with some data; in order to do that, you have to implement the `initialData()` method:

```php
protected function initialData(): array
{
    return [
        "message" => "Some initial value"
    ];
}
```

For an Instance command, add the `$instanceId` as a parameter:

```php
 protected function initialData($instanceId): array
 {
     [...]
 }
```

This method must return an array of formatted values, like for a regular [Entity Form](building-form.md). This means you
can [transform data](how-to-transform-data.md) here:

```php
protected function initialData($instanceId): array
{
    return $this
        ->setCustomTransformer("message", function($value, Spaceship $instance) {
            return sprintf("Message #%s:", $instance->messages_sent_count);
        })
        ->transform(
            Spaceship::findOrFail($instanceId)
        );
}
```

Note that in both cases (Entity or Instance Command), you can access to the EntityList querystring via the request.

### Configure the command (confirmation text, description, form modal title...)

You can tweak this in an optional `buildCommandConfig()` function:

```php
public function buildCommandConfig(): void
{
    $this->configureConfirmationText("Sure, really?")
        ->configureDescription("This action will send a text message to your boss")
        ->configureFormModalTitle("Text message");
}
```

Here is the full list of available methods:

- `configureConfirmationText(string $confirmationText)`: is set the Command will ask a confirmation to the user before
  executing
- `configureDescription(string $description)`: this text will appear under the Command label
- `configureFormModalTitle(string $formModalTitle)`: if the Command has a Form, the title of the modal will be its
  label, or `$formModalTitle` if defined
- `configurePageAlert(string $template, string $alertLevel = null, string $fieldKey = null, bool $declareTemplateAsPath = false)`:
  display a dynamic message above the Form; [see detailed doc](page-alerts.md)

### Command return types

Finally, let's review the return possibilities. After a Command has been executed, the code must return a "command
return" to tell to the front what to do next. There are six of them:

- `return $this->info("some text")`: displays the entered text in a modal.
- `return $this->reload()`: reload the current entity list (with context).
- `return $this->refresh(1)`*: refresh only the instance with an id on `1`. We can pass an id array also to refresh more
  than one instance.
- `return $this->view("view.name", ["some"=>"params"])`: display a view right in Sharp. Useful for page previews.
- `return $this->link("/path/to/redirect")`: redirect to the given path
- `return $this->download("path", "diskName")`: the browser will download the specified file.
- `return $this->streamDownload("path", "name")`: the browser will stream the specified file.

\* In order for `refresh()` to work properly, your Entity List's  `getListData()` will be called, and `$params` will return all the wanted `id`s with `specificIds()`. Here's a code example:

```php
function getListData()
{
    $spaceships = Spaceship::distinct();

    if($params->specificIds()) {
        $spaceships->whereIn("id", $this->queryParams->specificIds());
    }

    [...]
}
```

## Configure the Command

Once the Command class is written, we must add it to the EntityList:

```php
function getInstanceCommands(): ?array
{
    return [
        SpaceshipSendMessage::class
    ];
}

function getEntityCommands(): ?array
{
    return [
        SpaceshipReload::class
    ];
}
```

or to the Dashboard:

```php
function getDashboardCommands(): ?array
{
    return [
        TravelsDashboardDownloadCommand::class
    ];
}
```

You can optionally specify a command key (like "message" in the example); Sharp will use the command class name if it is missing. For the command itself, you can type a class name, a class instance or a Closure.

## Handle authorizations

Of course, it's often mandatory to add authorizations to a Command. Here's how to do that:


### Authorizations for entity Commands

Implement the `authorize(): bool` function, which must return a boolean to allow or disallow the Command execution,
based on any logic of yours. It can be for instance:

```php
public function authorize(): bool
{
    return auth()->user()->hasGroup("boss");
}
```

### Authorizations for instance Commands

For instance Commands we have to know the instance involved, which means the signature is different:

```php
public function authorizeFor($instanceId): bool
{
    return Spaceship::findOrFail($instanceId)->owner_id == auth()->id();
}
```

### Define an entity Command as "primary"

An EntityList can declare one (and only one) of its entity Commands as "primary". In this case, the command will appear at the top, right next to the creation button ("New..."). The idea is to provide more visibility to an important Command, but could also be to replace the creation button entirely (you need to remove the "create" authorization to achieve this). 

```php
function buildListConfig(): void
{
    $this->configurePrimaryEntityCommand(InviteNewUser::class);
}
```

A use case could be to provide a Command with a form for the "create" task, leaving the real Form only for update.

## Commands for Show Page

Show Page can only define instance commands (obviously); apart from that, the API is the same.

It's a common pattern to reuse the same instance commands in an EntityList and in a Show Page.
One small difference is that `reload()` action is treated as a `refresh()`.

## Commands for Dashboard

Dashboard can use the power of Commands too. The API is very similar, here are the differences:

- There is no Instance or Entity distinction; a command handler must extend `Code16\Sharp\Dashboard\Commands\DashboardCommand`.
- A Dashboard Command can not return a `refresh()` action, since there is no Instance.
