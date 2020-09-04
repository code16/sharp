<template>
    <component
        :is="tag"
        class="SharpButton"
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
            type: String,
            outline: Boolean,
            danger: Boolean,
            small: Boolean,
            href: String,
        },
        computed:{
            classes() {
                return [
                    this.type ? `SharpButton--${this.type}` : '', {
                        'SharpButton--secondary': this.outline,
                        'SharpButton--danger': this.danger,
                        'SharpButton--sm': this.small
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