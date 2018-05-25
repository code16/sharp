# Custom form field

## Setup front

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
     // 'type of the field' : Component
        'texticon': TextIcon
    }
})
```
**/!\\**  
*Vue is exposed to the window scope, it's  the current Vue version used by sharp (cf. package.json)  
Using any other Vue plugins in this file may harm your back-office, at your own risks!*  
**/!\\**

#### Add to mix

*webpack.mix.js*
```js
mix.js('/resources/assets/js/sharp-plugin.js', '/public/js')
```


## Setup back

<!-- TODO Back-end tutorial -->