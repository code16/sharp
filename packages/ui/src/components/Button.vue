<template>
    <component
        :is="tag"
        class="SharpButton btn"
        :class="classes"
        :href="href"
        :disabled="disabled"
        v-on="$listeners"
    >
        <slot />
    </component>
</template>

<script>
    export default {
        props: {
            variant: {
                type: String,
                default: 'primary',
            },
            text: Boolean,
            outline: Boolean,
            small: Boolean,
            large: Boolean,
            active: Boolean,
            block: Boolean,
            href: String,
            disabled: Boolean,
        },
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
            tag() {
                return this.href ? 'a' : 'button';
            },
        },
        methods: {
            focus() {
                this.$el.focus();
            },
        },
    }
</script>
