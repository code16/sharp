<template>
    <Multiselect
        :id="id"
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
            <ClearButton class="SharpSelect__clear-button" @click="remove" />
        </template>
        <template v-slot:tag="{ option, remove }" :key="option">
            <span class="multiselect__tag">
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
    import { Multiselect, ClearButton } from '@sharp/ui';
    import { onLabelClicked } from "../../../util/accessibility";

    export default {
        components: {
            Multiselect,
            ClearButton,
        },
        props: {
            id: String,
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
                return this.options?.map(o => o.id);
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
                this.$refs.multiselect.activate();
            },
            handleInput(val) {
                this.$emit('input', val);
            },
        },
        mounted() {
            onLabelClicked(this, this.id, () => {
                this.$el.focus();
            });
        },
    }
</script>
