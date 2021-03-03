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
        computed:{
            classes() {
                let variantClass = 'btn';
                if(this.outline) {
                    variantClass = `${variantClass}-outline`;
                }
                variantClass = `${variantClass}-${this.type}`;

                return [
                    variantClass,
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
