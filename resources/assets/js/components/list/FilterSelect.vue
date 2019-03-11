<template>
    <span class="SharpFilterSelect"
          :class="{
              'SharpFilterSelect--open':opened,
              'SharpFilterSelect--empty':empty,
              'SharpFilterSelect--multiple':multiple,
              'SharpFilterSelect--searchable':searchable
          }"
          tabindex="0"
    >
        <!-- dropdown & search input -->
        <sharp-autocomplete
            class="SharpFilterSelect__select"
            :value="autocompleteValue"
            :local-values="values"
            :search-keys="searchKeys"
            :list-item-template="template"
            :placeholder="l('entity_list.filter.search_placeholder')"
            :multiple="multiple"
            :hide-selected="multiple"
            :allow-empty="!required"
            :preserve-search="false"
            :show-pointer="false"
            :searchable="searchable"
            no-result-item
            mode="local"
            ref="autocomplete"
            @multiselect-input="handleAutocompleteInput"
            @close="close"
        />
        <span class="SharpFilterSelect__text" @mousedown="handleMouseDown">
            {{name}}
        </span>

        <!-- value text & tags -->
        <sharp-select
            class="SharpFilterSelect__select"
            :value="value"
            :options="values"
            :multiple="multiple"
            :clearable="!required"
            :inline="false"
            :unique-identifier="filterKey"
            placeholder=" "
            ref="select"
            @input="handleSelect"
            @mousedown.native="handleMouseDown"
        />
    </span>
</template>

<script>
    import SharpDropdown from '../dropdown/Dropdown';
    import SharpSelect from '../form/fields/Select';
    import SharpAutocomplete from '../form/fields/Autocomplete';

    import { Localization } from '../../mixins';


    export default {
        name: 'SharpFilterSelect',
        mixins: [Localization],
        components: {
            SharpDropdown,
            SharpSelect,
            SharpAutocomplete
        },
        props: {
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
            template: String,

            filterKey: String,
        },
        data() {
            return {
                opened: false
            }
        },
        computed: {
            optionById() {
                return this.values.reduce((res, v)=> ({
                    ...res, [v.id]: v
                }), {});
            },
            empty() {
                return this.value == null || this.multiple && !this.value.length;
            },
            autocompleteValue() {
                return this.multiple ? (this.value||[]).map(value=>this.optionById[value]) : this.optionById[this.value];
            }
        },
        methods: {
            handleSelect(value) {
                this.$emit('input', value);
            },
            handleAutocompleteInput(value) {
                this.$emit('input', this.multiple ? value.map(v=>v.id) : (value||{}).id);
            },
            handleMouseDown() {
                if(this.opened) {
                    this.close();
                } else {
                    this.open();
                }
            },
            open() {
                this.opened = true;
                this.$emit('open');
                this.$nextTick(this.showDropdown);
            },
            close() {
                this.opened = false;
                this.$emit('close');
                this.$nextTick(this.blur);
            },
            showDropdown() {
                let { autocomplete:{ $refs: { multiselect } } } = this.$refs;
                multiselect.activate();
            },
            blur() {
                let { select:{ $refs: { multiselect } } } = this.$refs;
                multiselect.deactivate();
            },
        }
    }
</script>