<template>
    <div class="SharpFilterSelect"
          :class="{
              'SharpFilterSelect--open':opened,
              'SharpFilterSelect--empty':empty,
              'SharpFilterSelect--multiple':multiple,
              'SharpFilterSelect--searchable':searchable,
              'SharpFilterSelect--underlined':!global
          }"
    >
        <!-- dropdown & search input -->
        <Autocomplete
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
            :read-only="disabled"
            nowrap
            no-result-item
            mode="local"
            ref="autocomplete"
            style="max-width: 0"
            @multiselect-input="handleAutocompleteInput"
            @close="close"
        />

        <FilterControl :label="label" @click="handleClicked">
            <!-- value text & tags -->
            <Select
                class="SharpFilterSelect__select form-select text-wrap"
                :value="value"
                :options="values"
                :multiple="multiple"
                :clearable="!required"
                :read-only="disabled"
                :inline="false"
                placeholder=" "
                ref="select"
                @input="handleSelect"
            >
            </Select>
        </FilterControl>
    </div>
</template>

<script>
    import { Autocomplete, Select } from 'sharp-form'
    import { Localization } from 'sharp/mixins';
    import FilterControl from '../FilterControl.vue';

    export default {
        name: 'SharpFilterSelect',
        mixins: [Localization],
        components: {
            Select,
            Autocomplete,
            FilterControl,
        },
        props: {
            label: String,
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
            disabled: Boolean,
            global: Boolean,
        },
        data() {
            return {
                opened: false,
                // dev only, check it in devtools to keep dropdown open
                debug: false,
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
            handleClicked() {
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
                if(this.debug) {
                    return;
                }
                this.opened = false;
                this.$emit('close');
                this.$nextTick(this.blur);
            },
            showDropdown() {
                let { autocomplete:{ $refs: { multiselect } } } = this.$refs;
                multiselect.activate();

                if(this.debug) {
                    this.unwatch && this.unwatch();
                    this.unwatch = multiselect.$watch('isOpen', function (isOpen) { if(!isOpen)this.isOpen = true; }, { sync: true });
                }
            },
            blur() {
                this.$refs.select.blur();
            },
        }
    }
</script>
