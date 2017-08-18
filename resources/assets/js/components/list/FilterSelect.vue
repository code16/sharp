<template>
    <span class="SharpFilterSelect">
        <sharp-dropdown ref="dropdown" @opened="updateScroll">
            <template slot="text">
                {{name}}<span style="font-weight:normal">{{ valueString ? ` | ${valueString}` : '' }}</span>
            </template>
            <sharp-select :value="value"
                          :options="options"
                          :multiple="multiple"
                          :clearable="!required"
                          :inline="false"
                          :unique-identifier="filterKey"
                          display="list"
                          disable-focus
                          ref="select"
                          @input="handleSelect">
            </sharp-select>
        </sharp-dropdown>
    </span>
</template>

<script>
    import Dropdown from '../dropdown/Dropdown';
    import Select from '../form/fields/Select';

    import { AutoScroll } from '../../mixins';


    export default {
        name: 'SharpFilterSelect',
        mixins: [AutoScroll],
        components: {
            [Dropdown.name]: Dropdown,
            [Select.name]: Select
        },
        props: {
            filterKey: {
                type: String,
                required: true
            },
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
            },
            multiple: Boolean,
            required: Boolean
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
            },


            autoScrollOptions() {
                return {
                    list: this.$el.querySelector('.SharpDropdown__list'),
                    item: _ => this.$refs.select.$el.querySelector('input:checked')
                }
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
            },
        },
    }
</script>