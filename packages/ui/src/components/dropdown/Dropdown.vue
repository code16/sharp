<template>
    <b-dropdown
        class="SharpDropdown"
        :toggle-class="toggleClass"
        :disabled="disabled"
        :no-caret="!showCaret"
        :offset="1"
        :boundary="boundary"
        :popper-opts="popperOptions"
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
            BDropdown: {
                extends: BDropdown,
                computed: {
                    boundaryClass: () => null,
                },
            },
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
        data() {
            return {
                boundary: 'scrollParent',
            }
        },
        computed: {
            ...Button.computed,
            /**
             * button variant is defined in classes
             */
            toggleClass() {
                return [this.classes, this.$attrs['toggle-class']]
            },
            popperOptions() {
                return {
                    modifiers: {
                        preventOverflow: {
                            padding: 8,
                            priority: ['left', 'right'],
                        }
                    }
                }
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
            if(this.$el.closest('[data-popover-boundary]')) {
                this.boundary = this.$el.closest('[data-popover-boundary]');
            }
        },
    }
</script>
