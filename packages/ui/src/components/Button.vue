<template>
    <component
        :is="tag"
        class="SharpButton btn"
        :class="classes"
        :href="href"
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
            active: Boolean,
            href: String,
        },
        computed: {
            variantClass() {
                return `btn${this.outline ? '-outline' : ''}-${this.variant}`;
            },
            classes() {
                return [
                    this.variantClass,
                    {
                        'btn-sm': this.small,
                        'btn-text': this.text,
                        'active': this.active,
                    }
                ]
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
