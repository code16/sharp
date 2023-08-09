<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { SelectFilterData } from "@/types";
    import { computed } from "vue";

    const props = defineProps<{
        value: SelectFilterData['value'],
        filter: Omit<SelectFilterData, 'value'>,
        disabled?: boolean,
        global?: boolean,
    }>();

    const emit = defineEmits(['input'])

    const modelValue = computed({
        get() {
            return props.value;
        },
        set(value) {
            emit('input', value);
        }
    });
</script>

<template>
    <div class="SharpFilterSelect"
          :class="{
              'SharpFilterSelect--open': opened,
              'SharpFilterSelect--empty': empty,
              'SharpFilterSelect--multiple': filter.multiple,
              'SharpFilterSelect--searchable': filter.searchable,
              'SharpFilterSelect--underlined': !global
          }"
    >
        <select v-model="modelValue" :multiple="filter.multiple">
            <template v-for="filterValue in filter.values">
                <option :value="filterValue.id">{{ filterValue.label }}</option>
            </template>
        </select>
<!--        &lt;!&ndash; dropdown & search input &ndash;&gt;-->
<!--        <Autocomplete-->
<!--            class="SharpFilterSelect__select"-->
<!--            :value="autocompleteValue"-->
<!--            :local-values="values"-->
<!--            :search-keys="searchKeys"-->
<!--            :list-item-template="template"-->
<!--            :placeholder="__('sharp::entity_list.filter.search_placeholder')"-->
<!--            :multiple="multiple"-->
<!--            :hide-selected="multiple"-->
<!--            :allow-empty="!required"-->
<!--            :preserve-search="false"-->
<!--            :show-pointer="false"-->
<!--            :searchable="searchable"-->
<!--            :read-only="disabled"-->
<!--            nowrap-->
<!--            no-result-item-->
<!--            mode="local"-->
<!--            ref="autocomplete"-->
<!--            style="max-width: 0"-->
<!--            @multiselect-input="handleAutocompleteInput"-->
<!--            @close="close"-->
<!--        />-->

<!--        <FilterControl :label="label" @click="handleClicked">-->
<!--            &lt;!&ndash; value text & tags &ndash;&gt;-->
<!--            <Select-->
<!--                class="SharpFilterSelect__select form-select text-wrap"-->
<!--                :value="value"-->
<!--                :options="values"-->
<!--                :multiple="multiple"-->
<!--                :clearable="!required"-->
<!--                :read-only="disabled"-->
<!--                :inline="false"-->
<!--                placeholder=" "-->
<!--                ref="select"-->
<!--                @input="handleSelect"-->
<!--            >-->
<!--            </Select>-->
<!--        </FilterControl>-->
    </div>
</template>

<script lang="ts">
    import { Autocomplete, Select } from '@sharp/form'
    import FilterControl from '../FilterControl.vue';

    export default {
        components: {
            Select,
            Autocomplete,
            FilterControl,
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
