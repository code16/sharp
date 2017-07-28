<template>
    <sharp-dropdown class="SharpFilterSelect" :text="name" ref="dropdown">
        <sharp-select :value="value" :options="options" :multiple="multiple"
                      display="list" @input="handleSelect" :inline="false" disable-focus>
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
                type: [String, Number, Array],
                required: true
            },
            multiple: Boolean
        },
        computed: {
            options() {
                return Object.keys(this.values).map(key => ({id:key, label:this.values[key]}));
            }
        },
        methods: {
            handleSelect(value) {
                this.$emit('input', value);
                this.$refs.dropdown.$el.focus();

            }
        }
    }
</script>