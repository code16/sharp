<template>
    <component :is="component" v-bind="templateData">
        <slot />
    </component>
</template>

<script>
    export default {
        props: {
            name: String,
            templateData: Object,
            templateProps: Array,
            template: String,
        },

        computed: {
            component() {
                return {
                    name: 'TemplateRendererContent',
                    template: `<div class="SharpTemplate">${this.template ?? ''}</div>`,
                    props: [
                        ...(this.templateProps || []),
                        ...Object.keys(this.templateData ?? {}),
                    ],
                    mounted() {
                        const isEmpty = !this.$el.children?.length && !this.$el.innerText?.trim();
                        this.$emit('content-change', {
                            isEmpty,
                        });
                        if(this.$el.children?.length > 0) {
                            this.$el.classList.add('SharpTemplate--has-children');
                        }
                    },
                }
            },
        }
    }
</script>
