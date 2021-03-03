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
            type: {
                type: String,
                default: 'primary',
            },
            outline: Boolean,
            small: Boolean,
            href: String,
        },
        computed: {
            variantClass() {
                return `btn${this.outline ? '-outline' : ''}-${this.type}`;
            },
            classes() {
                return [
                    this.variantClass,
                    {
                        'btn-sm': this.small
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
