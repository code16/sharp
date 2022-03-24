<template>
    <b-dropdown
        class="SharpDropdown"
        :toggle-class="toggleClass"
        :disabled="disabled"
        :no-caret="!showCaret"
        :offset="1"
        :boundary="boundary"
        variant="custom"
        no-flip
        v-bind="$attrs"
        v-on="$listeners"
        ref="dropdown"
    >
        <template v-slot:button-content>
            <slot name="text">{{ text }}</slot>
        </template>

        <slot :hide="hide" />
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
            text: [Boolean, String],
            showCaret: {
                type: Boolean,
                default: true
            },
            title: String,
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
            boundary() {
                return document.querySelector('[data-popover-boundary]');
            },
        },
        methods: {
            hide() {
                this.$refs.dropdown.hide();
            },
        },
        mounted() {
            if(this.title) {
                this.$el.querySelector('.dropdown-toggle').setAttribute('title', this.title);
            }
        },
    }
</script>
