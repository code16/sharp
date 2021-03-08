<template>
    <Multiselect
        :value="value"
        :searchable="false"
        :options="multiselectOptions"
        :multiple="multiple"
        :hide-selected="multiple"
        :close-on-select="!multiple"
        :custom-label="multiselectLabel"
        :placeholder="placeholder"
        :disabled="readOnly"
        :max="maxSelected"
        :allow-empty="clearable"
        @input="handleInput"
        @open="$emit('open')"
        @close="$emit('close')"
        ref="multiselect"
    >
        <template v-if="hasClearButton" v-slot:caret>
            <button class="SharpSelect__clear-button" type="button" @mousedown.stop.prevent="remove()">
                <svg class="SharpSelect__clear-button-icon"
                    aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                    <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                </svg>
            </button>
        </template>
        <template v-slot:tag="{ option, remove }">
            <span class="multiselect__tag" :key="option">
                <span>{{ multiselectLabel(option) }}</span>
                <i aria-hidden="true" tabindex="1" @keypress.enter.prevent="remove(option)" @mousedown.prevent.stop="remove(option)" class="multiselect__tag-icon"></i>
            </span>
        </template>
        <template v-slot:option>
            <slot name="option" />
        </template>
    </Multiselect>
</template>

<script>
    import { Multiselect } from 'sharp-ui';

    export default {
        components: {
            Multiselect,
        },
        props: {
            value: [Array, String, Number],
            options: Array,
            labels: Object,
            multiple: Boolean,
            clearable: Boolean,
            placeholder: String,
            maxSelected: Number,
            readOnly: Boolean,
        },
        computed: {
            multiselectOptions() {
                return this.options.map(o => o.id);
            },
            hasClearButton() {
                return this.clearable && !this.multiple && this.value != null;
            },
        },
        methods: {
            multiselectLabel(id) {
                return this.labels[id];
            },
            remove() {
                this.$emit('input', null);
            },
            handleInput(val) {
                this.$emit('input', val);
            },
        }
    }
</script>
