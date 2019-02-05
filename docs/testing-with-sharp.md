# Testing with Sharp

Sharp provide a fexw assertions and helpers to help you testing your Sharp code.


## The `SharpAssertions` trait

The `Code16\Sharp\Utils\Testing\SharpAssertions` trait is intended to be used in a Feature test. It has to be intialized, like this:

```php
    protected function setUp()
    {
        parent::setUp();

        $this->initSharpAssertions();
    }
```

### Helpers

First, the trait adds a few helpers:


#### `loginAsSharpUser($user)`

Logs in the given user as a Sharp user.


#### `getSharpForm(string $entityKey, $instanceId = null)`

Call the Sharp API to display the form for the Entity `$entityKey`. If `$instanceId` is provided, it will be an update form, and otherwise a create one.


#### `updateSharpForm(string $entityKey, $instanceId, array $data)`

Call the Sharp API to update the Entity `$entityKey` of id `$instanceId`, with `$data`.


#### `storeSharpForm(string $entityKey, array $data)`

Call the Sharp API to store a new Entity `$entityKey` with `$data`.


#### `callEntityCommand(string $entityKey, string $commandKey, array $data)`

Call the `$commandKey` Entity Command with the optional `$data`.


#### `callInstanceCommand(string $entityKey, $instanceId, string $commandKey, array $data)`

Call the `$commandKey` Instance Command with the optional `$data`.


### Assertions

First, and of course, you can use regular assertions, like for instance:

```php
    $this->updateSharpForm(
            "orders", 
            $order->id, 
            array_merge($order->toArray(), [
                "client" => "test",
                "payment_delay" => 10
            ]))
        ->assertStatus(200);
```

But sometimes you'll want to test some specific Sharp things. Here's the list of custom assertions added by the `SharpAssertions` trait:

#### `assertSharpHasAuthorization($authorization)` and `assertSharpHasNotAuthorization($authorization)`

Example:

```php
    $this->getSharpForm("orders", $order->id)
         ->assertSharpHasAuthorization("update")
         ->assertSharpHasAuthorization("delete");
```

#### `assertSharpFormHasFields($names)`

Example:

```php
    $this->getSharpForm("orders")
         ->assertSharpFormHasFields([
               "number", "client"
         ]);
```

#### `assertSharpFormHasFieldOfType($name, $formFieldClassName)`

Example:

```php
    $this->getSharpForm("orders", $order->id)
         ->assertSharpFormHasFieldOfType(
             "number", SharpFormTextField::class
         );
```

#### `assertSharpFormDataEquals($name, $value)`

Example:

```php
    $this->getSharpForm("orders", $order->id)
         ->assertSharpFormDataEquals("number", $order->number);
```


## Browser testing (Laravel Dusk)

TBD