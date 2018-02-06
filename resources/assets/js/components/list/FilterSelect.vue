<template>
    <span class="SharpFilterSelect"
          :class="{
              'SharpFilterSelect--open':opened,
              'SharpFilterSelect--empty':empty,
              'SharpFilterSelect--multiple':multiple,
              'SharpFilterSelect--searchable':searchable
          }"
          tabindex="0" @click="open"
    >
        <sharp-autocomplete
                class="SharpFilterSelect__select"
                :value="autocompleteValue"
                :local-values="values"
                :search-keys="searchKeys"
                :list-item-template="template"
                :placeholder="l('entity_list.filter.search_placeholder')"
                :multiple="multiple"
                no-result-item hide-selected
                mode="local"
                ref="autocomplete"
                @input="handleAutocompleteSelect"
                @close="close"
        />
        <span class="SharpFilterSelect__text">
            {{name}}<span v-if="!empty" style="font-weight:normal">&nbsp;&nbsp;</span>
        </span>

        <sharp-select
            class="SharpFilterSelect__select"
            :value="value"
            :options="values"
            :multiple="multiple"
            :clearable="!required"
            :inline="false"
            :unique-identifier="filterKey"
            :placeholder="fixZeroValuePlaceholder"
            ref="select"
            @input="handleSelect"
        />
    </span>
</template>

<script>
    import Dropdown from '../dropdown/Dropdown';
    import Select from '../form/fields/Select';
    import Autocomplete from '../form/fields/Autocomplete';

    import { Localization } from '../../mixins';


    export default {
        name: 'SharpFilterSelect',
        mixins:[Localization],
        components: {
            [Dropdown.name]: Dropdown,
            [Select.name]: Select,
            [Autocomplete.name]: Autocomplete
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
            required: Boolean,
            searchable: Boolean,
            searchKeys: Array,
            template: String
        },
        data() {
            return {
                opened: false
            }
        },
        computed: {
            empty() {
                return this.value == null || this.multiple && !this.value.length;
            },
            fixZeroValuePlaceholder() {
                return !this.multiple ? (this.values.find(option => option.id===0)||{}).label : '';
            },
            autocompleteValue() {
                return this.values.find(opt=>opt.id===this.value);
            }
        },
        methods: {
            handleSelect(value) {
                this.$emit('input', value);
            },
            handleAutocompleteSelect(value) {
                if(value == null)return;
                this.$emit('input', this.multiple ? [...this.value, value.id] : value.id);
            },
            open() {
                this.opened = true;
                this.showMultiselect();
            },
            close() {
                this.opened =false;
            },
            showMultiselect() {
                let { autocomplete:{ $refs: { multiselect } } } = this.$refs;
                multiselect.activate();
            }
        }
    }
</script>