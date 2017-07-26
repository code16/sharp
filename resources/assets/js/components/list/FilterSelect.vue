<template>
    <sharp-dropdown :text="name">
        <sharp-select :value="value" :options="options" :multiple="multiple"
                      display="list" @input="$emit('input',$event)">
        </sharp-select>
    </sharp-dropdown>
</template>

<script>
    import Dropdown from '../dropdown/Dropdown';
    import Select from '../form/fields/Select';

    export default {
        name: 'SharpFilterSelect',
        components: {
            [Dropdown.name]: Dropdown,
            [Select.name]: Select
        },
        props: {
            name : {
                type: String,
                required: true
            },
            values: {
                type: Object,
                required: true
            },
            value: {
                type: [String, Array],
                required: true
            },
            multiple: Boolean
        },
        computed: {
            options() {
                return Object.keys(this.values).map(key => ({id:key, label:this.values[key]}));
            }
        }
    }
</script>