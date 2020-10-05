<template>
    <rendered-template
        class="SharpTemplate"
        :name="name"
        :template="template"
        :template-data="templateData"
        :template-props="templateProps"
    >
    </rendered-template>
</template>

<script>
    export default {
        name: 'SharpTemplate',

        components: {
            RenderedTemplate: {
                functional: true,
                props: {
                    name:String, template:String, templateProps:Array, templateData:Object
                },
                render(h, { props, data }) {
                    const { name ,template, templateProps, templateData } = props;
                    return h({
                            name: `SharpTemplate${name}`,
                            template:`<div>${template}</div>`,
                            props: templateProps
                        }, {
                        ...data,
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
                return Object.keys(this.templateData||{});
            },
        }
    }
</script>