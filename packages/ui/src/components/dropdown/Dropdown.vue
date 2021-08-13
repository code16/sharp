<template>
    <b-dropdown
        class="SharpDropdown"
        :toggle-class="toggleClass"
        :disabled="disabled"
        :no-caret="!showCaret"
        :offset="1"
        variant="custom"
        v-bind="$attrs"
        v-on="$listeners"
    >
        <template v-slot:button-content>
            <span class="d-inline-block">
                <slot name="text">{{ text }}</slot>
            </span>
        </template>

        <template v-slot:default="props">
            <slot v-bind="props" />
        </template>
    </b-dropdown>
</template>

<script>
    import { BDropdown } from 'bootstrap-vue';
    import Button from "../Button"

    export default {
        name: 'SharpDropdown',
        components: {
            BDropdown,
        },
        props: {
            ...Button.props,
            text: String,
            showCaret: {
                type: Boolean,
                default: true
            },
            disabled: Boolean,
        },
        computed: {
            ...Button.computed,
            /**
             * button variant is defined in classes
             */
            toggleClass() {
                return this.classes;
            },
        },
    }
</script>
