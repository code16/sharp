# Multi-Forms

Let's say you want to handle different variants for an entity in one Entity List.

For instance, maybe you want to display sold cars on an Entity List: easy enough, you create a `Car` entity, list and form. But you want the to handle different form fields for cars with an internal combustion engine and those with an electric engine; you can of course use a form and [conditional display](building-form.md#conditional-display) to achieve this, but in a case where there are many differences, the best option may be to split the Entity in two (or more) Forms. That's Multi-Form.

## Write the Form classes

Following up the car example, we would write two Form classes: `CombustionCarForm` and `ElectricCarForm`, maybe. They are regular `SharpForm` classes, as [described here](building-form.md).

::: tip
Note that You'll probably be able to regroup some common code in a trait or by inheritance: it's up to you.
:::

## Configuration

Once the classes are written, you must declare the forms in the entity class:

```php
class CarEntity extends SharpEntity
{
    protected ?string $list = CarList::class;
    protected string $label = 'Car';
    
    public function getMultiforms(): array
    {
        return [
            'combustion' => [\App\Sharp\CombustionCarForm::class, 'Combustion car'],
            'electric' => [\App\Sharp\ElectricCarForm::class, 'Electric car'],
        ];
    }
}
```

The expected return of the `getMultiforms()` method is an array with: 
- the subentity key as key: this is the value of the split attribute, to disambiguate each type (see below),
- and, as value, an array with the Form class and the subentity label.

At this stage, you need only one more thing: configure the Entity List to handle Multi-Form.

## The Entity List

Now we want to "merge" our Car entity in the Entity List, and allow the user to create or edit either a combustion or an electric car. With the configuration added to the entity class, at the previous step, we already have a dropdown button replacing the "New" button, each value leading to the right Form.

You must configure an instance attribute to disambiguate each type: each instance must have his attribute valuated either with "electric" or "combustion", in our example.

You declare this attribute in the Entity List `buildListConfig()` method:

```php
class CarList extends SharpEntityList
{
    // [...]
    function buildListConfig(): void
    {
        $this->configureMultiformAttribute('engine');
    }
}
```

Here, the `engine` attribute must be filled for each Car instance. So how you do that? Obviously, the first way is to keep the same attribute you use in your database: in many cases, you already have this `engine` value in a column. If not, or if the value is something less readable (an ID for instance), use a [custom transformer](how-to-transform-data.md):

```php
class CarList extends SharpEntityList
{
    // [...]
    function getListData(): array
    {
        return $this
            ->setCustomTransformer('engine', function($value, Car $car) {
                return $car->motor === 'EV' ? 'electric' : 'combustion';
            })
            ->transform(Car::get());
    }
}
```
