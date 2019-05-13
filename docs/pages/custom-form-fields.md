# Custom form field

## On the front side

### Creating the Vue component

Example of custom sharp field:

```vue
<template>
    <div>
        <input class="SharpText" :value="value" @change="handleChanged">
        <i class="fa" :class="icon"></i>
    </div>
</template>

<script>
    export default {
        props: {
            value: String, // field value
            icon: String   // custom added props (given in field definition)
        },
        methods: {
            handleChanged(e) {
                this.$emit('input', e.target.value); // emit input when the value change, form data is updated
            }
        }
    }
</script>
```

#### Exposed Props

| Prop            | Description                                 |
|-----------------|---------------------------------------------|
| value           | value of the field, *required*                                            |
| fieldKey        | field key in the form                       |
| locale          | current locale, `undefined` if the form is not localized |
| uniqueIdentifier| Global unique field identifier, corresponding to the laravel error key |
| ...             | *All other props given in the field definition* |

#### Listened events

| Event           | Description                                 | Parameters |
|-----------------|---------------------------------------------|------------|
|input            | Update the form data with the emitted value, <br>*the force option will change the value even if the field is read-only* | (value, { force: Boolean }) |


### Register the custom field

Add `sharp-plugin` npm package to your project:

```
npm install -D sharp-plugin
```

#### Sharp plugin file

Add a separated `.js` file in your project to register fields components :

*sharp-plugin.js*

```js
import Sharp from 'sharp-plugin';
import TextIcon from './components/TextIcon.vue';


Vue.use(Sharp, {
    customFields: {
        'textIcon': TextIcon
    }
})
```


> Warning: Vue is exposed to the window scope, it's  the current Vue version used by sharp (cf. package.json). Using any other Vue plugins in this file may harm your back-office, at your own risks!

#### Add to Laravel Mix

*webpack.mix.js*

```js
mix.js('/resources/assets/js/sharp-plugin.js', '/public/js')
```

> Warning: the file name must be sharp-plugin.js in order to ensure Sharp will find it.

You can `.version()` this JS file if you want to.


## On the back side

### Activate custom form fields in config

```php
// config/sharp.php

"extensions" => [
   "activate_custom_form_fields" => true
],

// ...
```


### Write the form field class and formatter

Next step is to build your form field class. It must extend `Code16\Sharp\Form\Fields\SharpFormField`.

Here's an example:

```php
class SharpCustomFomFieldTextIcon extends SharpFormField
{
    const FIELD_TYPE = "custom-textIcon";

    protected $icon;

    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new TextFormatter);
    }

    public function setIcon(string $iconName)
    {
        $this->icon = $iconName;

        return $this;
    }

    protected function validationRules()
    {
        return [
            "icon" => "required",
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            "icon" => $this->icon,
        ]);
    }
}
```

A few things to note:

- The `FIELD_TYPE` const must be "custom-" + yout custom field name, defined on the front side.
- To respect the Sharp API, you must define a static `make` function with at least the field key; this function must call the parent constructor, passing the `$key`, the `FIELD_TYPE` and a Formatter, which can also be a custom one (see [documentation](docs/building-entity-form.md#formatters) and `Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter` base class).
- `validationRules()` implementation is optional, but adviced.
- the `toArray()` function is mandatory, and must call `parent::buildArray()` with additional attributes.


### Use it

Next step is... using the new form field. Well, this should be the easiest part:

*in some `Code16\Sharp\Form\SharpForm` subclass:*

```php
function buildFormFields()
{
    $this->addField(
        SharpCustomFomFieldTextIcon::make("name")
            ->setLabel("Name")
            ->setIcon("fa-user")
    );
}
```

