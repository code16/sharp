# Commands

Commands in Sharp are a powerful way to integrate functional processes in the content management. They can be used for instance to re-send an order to the customer, on synchronize pictures of a product, or preview a page...

## Write the Command class

First we need to write a class for our Command. It must extend the `Code16\Sharp\EntityList\Commands\EntityCommand` abstract class (for "entity commands", more on that below), and implement two functions. 

First one is `label(): string`, and must simply return the text label of the Command, displayed to the user:

    /**
     * @return string
     */
    public function label(): string
    {
        return "Reload full list";
    }

The second one, `execute(EntityListQueryParams $params, array $data=[]): array` handles the work of the Command itself:

    /**
     * @param EntityListQueryParams $params
     * @param array $data
     * @return array
     */
    public function execute(
        EntityListQueryParams $params, 
        array $data=[]): array
    {
        return $this->reload();
    }

More on this `return $this->reload();` below.


### Command scope: instance or entity

The example above is an "entity" case: Command applies to a subset of entities, or all of them. The `EntityListQueryParams` object passed as a parameter (named `$params`) can be used to extract the context (search, page, filters, ...), just like in the `getListData()` of the EntityList.

To create an instance Command (relative to a specific instance), the Command class must extend `Code16\Sharp\EntityList\Commands\InstanceCommand`. The execute method signature is a little bit different:

    /**
     * @param string $instanceId
     * @param array $params
     * @return array
     */
    public function execute($instanceId, array $params = []): array
    {
        [...]
    }

Instead of an `EntityListQueryParams` object, we get an `$instanceId` parameter to identify the exact instance involved. The rest is the same, except for authorization detailed below.


### Add a Command form

The second parameter in the `execute()` function is an array named `$data`, which contains values entered by the user in a Command specific form. A use case might be to allow the user to enter a text to be sent to the customer with his invoice. In order to do that, we have first to write a `buildFormFields()` function in the Command class:

    function buildFormFields()
    {
        $this->addField(
            SharpFormTextareaField::make("message")
                ->setLabel("Message")

        )->addField(
            SharpFormCheckField::make("now", "Send right now?")
                ->setHelpMessage("Otherwise it will be sent next night.")
        );
    }

The API is the same as building a standard entity form (see [Building an Entity Form](building-entity-form.md)).

Once this method has been declared, a form will be prompted to the user as he clicks on the Command.

Then, is the `execute()` method, it's trivial to grab the entered value, and even to handle the validation:

    public function execute($instanceId, array $data= []): array
    {
        $this->validate($data, [
            "message" => "required"
        ]);
        
        $text = $data["message"];
        [...]
    }


### Command confirmation

To add a confirmation message before a Command is executed, simply add a `confirmationText()` method:

    public function confirmationText()
    {
        return "Sure, really?";
    }


### Command return types

Finally, let's review the return possibilities. After a Command has been executed, the code must return a "command return" to tell to the front what to do next. There are five of them:

- `return $this->info("some text")`: displays the entered text in a modal.
- `return $this->reload()`: reload the current entity list (with context).
- `return $this->refresh(1)`*: refresh only the instance with an id on `1`. We can pass an id array also to refresh more than one instance.
- `return $this->view("view.name", ["some"=>"params"])`: display a  view right in Sharp. Useful for page previews.
- `return $this->link("/path/to/redirect")`: redirect to the given path

\* In order for `refresh()` to work properly, your Entity List's  `getListData(EntityListQueryParams $params)` will be called, and `$params` will return all the wanted `id`s with `specificIds()`. Here's a code example:

    function getListData(EntityListQueryParams $params)
    {
        $spaceships = Spaceship::distinct();

        if($params->specificIds()) {
            $spaceships->whereIn("id", $params->specificIds());
        }
        
        [...]
    }


## Configure the Command

Once the Command class is written, we must add it to the EntityList configuration. This is straightforward:

    function buildListConfig()
    {
        $this->addEntityCommand("reload", SpaceshipReload::class)
            ->addInstanceCommand("message", SpaceshipSendMessage::class)
            [...]
    }



## Handle authorizations

Of course, it's often mandatory to add authorizations to a Command. Here's how to do that:


### Authorizations for entity Commands

Simply implement the `authorize():bool` function, which must return a boolean to allow or disallow the Command execution, based on any logic of yours. It can be for instance:

    public function authorize():bool
    {
        return sharp_user()->hasGroup("boss");
    }

Note that the `sharp_user()` helper returns the logged user (see [Authentication](authentication.md)).


### Authorizations for instance Commands

For instance Commands we have to know the instance involved, which means the signature is different:

    public function authorizeFor($instanceId): bool
    {
        return Spaceship::findOrFail($instanceId)->owner_id == sharp_user()->id;
    }

---

> Next chapter : [Entity states](entity-states.md)