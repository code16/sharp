<template>
    <div class="form-group">
        <label v-html="label"></label>
        <sharp-field v-bind="$props"></sharp-field>
        <div>{{helpMessage}}</div>
    </div>
</template>

<script>
    import Field from './Field';

    export default {
        name: 'SharpFieldContainer',
        components: {
            [Field.name]:Field
        },
        props : {
            ...Field.props,

            label: {
                type: String
            },
            helpMessage: {
                type: Array
            }
        }
    }
</script>