<template>
    <rendered-template :template="template"
                       :template-data="templateData"
                       :template-props="templateProps">
    </rendered-template>
</template>

<script>
    export default {
        name: 'SharpTemplate',

        components: {
            RenderedTemplate: {
                functional: true,
                render(h, { props }) {
                    const { template, templateProps, templateData } = props;
                    return h({
                            name: 'AAA',
                            template:`<div>${template}</div>`,
                            props: templateProps
                        }, {
                        props: templateData
                    });
                }
            }
        },

        props: {
            name: String,
            templateData: Object,
            template: String,
        },

        computed: {
            templateProps() {
                return Object.keys(this.templateData);
            },
        }
    }
</script>