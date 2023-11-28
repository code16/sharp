<script setup lang="ts">
    import { Link } from '@inertiajs/vue3';

    withDefaults(defineProps<{
        variant?: string,
        text?: boolean,
        outline?: boolean,
        small?: boolean,
        large?: boolean,
        active?: boolean,
        block?: boolean,
        href?: string,
        disabled?: boolean,
    }>(), {
        variant: 'primary'
    });
</script>

<template>
    <component
        :is="href ? Link : 'button'"
        class="SharpButton btn"
        :class="classes"
        :href="href"
        :disabled="disabled"
    >
        <slot />
    </component>
</template>

<script lang="ts">
    export default {
        computed: {
            variantClass() {
                return !this.hasTextStyle && this.variant
                    ? `btn${this.outline ? '-outline' : ''}-${this.variant}`
                    : null;
            },
            classes() {
                return [
                    this.variantClass,
                    {
                        'btn-sm': this.small,
                        'btn-lg': this.large,
                        'btn-text': this.hasTextStyle,
                        'btn-block': this.block,
                        'active': this.active,
                        'disabled': this.disabled,
                    }
                ]
            },
            hasTextStyle() {
                return this.text === true;
            },
        },
        methods: {
            focus() {
                this.$el.focus();
            },
        },
    }
</script>
