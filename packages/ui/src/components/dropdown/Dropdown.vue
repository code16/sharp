<template>
    <b-dropdown
        :toggle-class="classes"
        :disabled="disabled"
        :no-caret="!showCaret"
        variant="custom"
        v-bind="$attrs"
    >
        <template v-slot:button-content>
            <slot name="text">{{ text }}</slot>
        </template>

        <slot />
    </b-dropdown>
</template>

<script>
    import DropdownArrow from './Arrow';
    import { BDropdown } from 'bootstrap-vue';
    import Button from "../Button"

    export default {
        name: 'SharpDropdown',
        components: {
            DropdownArrow,
            BDropdown,
        },
        props: {
            text: String,
            showCaret: {
                type: Boolean,
                default: true
            },
            disabled: Boolean,
            ...Button.props,
        },
        computed: {
            ...Button.computed,
        },
    }
</script>
