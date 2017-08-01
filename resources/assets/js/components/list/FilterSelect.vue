<template>
    <sharp-dropdown class="SharpFilterSelect" ref="dropdown">
        <template slot="text">
            {{name}}<span style="font-weight:normal">{{ valueString ? ` | ${valueString}` : '' }}</span>
        </template>
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
            },
            valueString() {
                if(Array.isArray(this.value)) {
                    return this.value.map(key => this.values[key]).join(', ');
                }
                return this.value ? this.values[this.value] : '';
            }
        },
        methods: {
            handleSelect(value) {
                this.$emit('input', value);
                if(this.multiple)
                    this.$refs.dropdown.$el.focus();
                else {
                    setTimeout(_=>this.$refs.dropdown.$el.blur(),100);
                }
            }
        },
    }
</script>