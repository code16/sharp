# Multi-Forms

Let's say you want to handle different variant for an Entity, but in one Entity List. 

For instance, maybe you are a car-seller and you want to display on an Entity List all sold cars. Easy enough, you create a `Car` entity, list and form. But now let's say you want the to handle different form fields for cars with an internal combustion engine and those with an electric engine; you can of course use a single form and [conditional display](building-entity-form.md#conditional-display) to achieve this, but in a case where there are many differences, the best option is to split the Entity in two (or more) Forms. That's Multi-Form.

## Write the Form classes

Following up the car example, we would write two Form classes: `CombustionCarForm` and `ElectricCarForm`, maybe. They are regular `SharpForm` classes, as [described here](building-entity-form.md).

Note that you'll probably be able to regroup some common code in a trait or by inheritance: it's up to you.

Same goes for [Validators](building-entity-form.md#input-validation), if needed.


## Configuration

Once the classes written, you must to declare the forms in the sharp config file. So instead of:

```php
    // config/sharp.php
    
    return [
        "entities" => [
            "car" => [
                "list" => \App\Sharp\CarSharpList::class,
                "form" => \App\Sharp\CarSharpForm::class,
                "validator" => \App\Sharp\CarSharpValidator::class
            ]
        ]
    ];
```

You'll have something like:

```php
    // config/sharp.php
    
    return [
        "entities" => [
            "car" => [
                "list" => \App\Sharp\CarSharpList::class,
                "forms" => [
                  "combustion" => [
                    "form" => \App\Sharp\CombustionCarSharpForm::class,
                    "validator" => \App\Sharp\CombustionCarSharpValidator::class,
                  ],
                  "electric" => [
                    "form" => \App\Sharp\ElectricCarSharpForm::class,
                    "validator" => \App\Sharp\ElectricCarSharpValidator::class,
                  ]
                ]
            ]
        ]
    ];
```

At this stage, you need only one more thing: configure the Entity List to handle Multi-Form.


## The Entity List

Now we want to "merge" out Car entity in the Entity List, and allow the user to create or edit either a combustion or an electric car.

To achieve this final step, you'll have to first update the global configuration to add a label and an optional icon to each type:

```php
    // config/sharp.php
    
    return [
        "entities" => [
            "car" => [
                "list" => \App\Sharp\CarSharpList::class,
                "forms" => [
                  "combustion" => [
                    "label" => "Combustion car",
                    "icon" => "fa-truck"
                    [...]
                  ],
                  "electric" => [
                    "label" => "Electric car",
                    "icon" => "fa-car",
                    [...]
                  ]
                ]
            ]
        ]
    ];
```

This allow the "new" button to display a dropdown with each type, leading to the right Form.

Last, you must configure an instance attribute to disambiguate each type: each instance must have this attribute valuated either with "electric" or "combustion", in our example.

You declare this attribute in the Entity List `buildListConfig()` method:

```php
    function buildListConfig()
    {
        $this->setSearchable()
            ->setDefaultSort("name", "asc")
            ->setMultiformAttribute("engine")
            ->setPaginated();
    }
```

Here, the `engine` attribute must be filled for each instance car. So how you do that? Obviously, the first way is to keep the same attribute you use in your database: in many cases, you already have this `engine` value in a column. If not, or if the value is something less readable (an ID for instance), use a [custom transformer](how-to-transform-data.md):

```php
    function getListData(EntityListQueryParams $params)
    {
        return $this
            ->setCustomTransformer("engine", function($value, $car) {
                return $car->motor == "EV" ? "electric" : "combustion";
            })
            ->transform(Car::paginate(30));
    }
```

---

> next chapter: [The Dashboard](dashboard.md).