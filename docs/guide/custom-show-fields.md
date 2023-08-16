# Custom show field

## On the front side

### Creating the Vue component

Example of custom sharp field:

```vue
<template>
    <div>
        Name: {{ value }}
        <i class="fa" :class="icon"></i>
    </div>
</template>

<script>
    export default {
        props: {
            value: String, // field value
            icon: String,   // custom added props (given in field definition)
        },
    }
</script>
```

#### Exposed Props

| Prop            | Description                                 |
|-----------------|---------------------------------------------|
| value           | value of the field, *required*                                            |
| fieldKey        | field key in the show                       |
| emptyVisible    | boolean determined by the [->setShowIfEmpty()](building-show-page.md) method, true by default  |
| ...             | *All other props given in the field definition* |

#### Listened events

| Event           | Description                                 | Parameters |
|-----------------|---------------------------------------------|------------|
| visible-change | Update the field visibility |  Boolean |


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
import ShowTitle from './components/ShowTitle.vue';


Vue.use(Sharp, {
    customFields: {
        'title': ShowTitle
    }
})
```
**Important**: The key must be `'title'` for `FIELD_TYPE = "custom-title"`

Vue is exposed to the window scope, it's the current Vue version used by sharp (cf. package.json).
::: warning
It's not recommended to use other Vue plugins in this file because it may change the behavior of the Sharp front-end.
:::

#### With Laravel Mix

*webpack.mix.js*

```js
mix.js('/resources/assets/js/sharp-plugin.js', '/public/js')
```

::: warning
The file name must be **sharp-plugin.js** in order to ensure Sharp will find it.
:::

You can `.version()` this JS file if you want to.

#### With Vite

Publish views with:
```bash
php artisan vendor:publish --provider=Code16\\Sharp\\SharpServiceProvider --tag=views
```

Add your `.js` file to `resources/views/vendor/sharp/partials/plugin-scripts.blade.php`:

```blade
@vite('resources/js/sharp-plugin.js')
```

## On the back side

### Activate custom fields in config

```php
// config/sharp.php

'extensions' => [
   'activate_custom_fields' => true
],

// ...
```


### Write the show field class and formatter

Next step is to build your show field class. It must extend `Code16\Sharp\Show\Fields\SharpShowField`.

Here's an example:

```php
class SharpCustomShowFieldTitle extends SharpShowField
{
    const FIELD_TYPE = 'custom-title';

    protected int $level = 1;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE);
    }

    public function setTitleLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            'level' => 'required|integer|min:1|max:5',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'level' => $this->level,
        ]);
    }
}
```

A few things to note:

- The `FIELD_TYPE` const must be "custom-" + your custom field name, defined on the front side.
- To respect the Sharp API, you must define a static `make` function with at least the field key; this function must call the parent constructor, passing the `$key` and the `FIELD_TYPE`.
- `validationRules()` implementation is optional, but advised.
- the `toArray()` function is mandatory, and must call `parent::buildArray()` with additional attributes.


### Use it

Next step is using the new show field:

*in some `Code16\Sharp\Show\SharpShow` subclass:*

```php
function buildShowFields(FieldsContainer $showFields): void
{
    $showFields->addField(
        SharpCustomShowFieldTitle::make('name')
            ->setLevel(2)
    );
}
```

