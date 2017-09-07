<template>
    <span class="SharpFilterSelect"
          :class="{
          'SharpFilterSelect--open':opened,
          'SharpFilterSelect--empty':empty,
          'SharpFilterSelect--multiple':multiple}"
          @click="open">
        <span class="SharpFilterSelect__text">
            {{name}}<span v-if="!empty" style="font-weight:normal">&nbsp;&nbsp;</span>
        </span>
        <sharp-select class="SharpFilterSelect__select"
                      :value="value"
                      :options="values"
                      :multiple="multiple"
                      :clearable="!required"
                      :inline="false"
                      :unique-identifier="filterKey"
                      ref="select"
                      @input="handleSelect"
                      @open="opened=true"
                      @close="opened=false">
        </sharp-select>
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
                type: Array,
                required: true
            },
            value: {
                type: [String, Number, Array],
            },
            multiple: Boolean,
            required: Boolean
        },
        data() {
            return {
                opened: false
            }
        },
        computed: {
            options() {
                return Object.keys(this.values).map(key => ({id:key, label:this.values[key]}));
            },

            autoScrollOptions() {
                return {
                    list: this.$el.querySelector('.SharpDropdown__list'),
                    item: _ => this.$refs.select.$el.querySelector('input:checked')
                }
            },

            empty() {
                return !this.value || this.multiple && !this.value.length;
            }
        },
        methods: {
            handleSelect(value) {
                this.$emit('input', value);
            },
            open() {
                let { select:{ $refs: { multiselect } } } = this.$refs;
                multiselect.activate();
            }
        }
    }
</script>