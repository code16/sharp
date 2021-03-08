<template>
    <b-dropdown
        :toggle-class="classes"
        :disabled="disabled"
        :no-caret="!showCaret"
        offset="1"
        variant="custom"
        v-bind="$attrs"
    >
        <template v-slot:button-content>
            <span class="d-inline-block">
                <slot name="text">{{ text }}</slot>
            </span>
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
            ...Button.props,
            text: [Boolean, String],
            showCaret: {
                type: Boolean,
                default: true
            },
            disabled: Boolean,
        },
        computed: {
            ...Button.computed,
        },
    }
</script>
