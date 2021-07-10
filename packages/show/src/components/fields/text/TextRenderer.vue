<template>
    <component :is="component"></component>
</template>

<script>
    import ShowFile from '../File';

    export default {
        props: {
            content: String,
            fieldConfigIdentifier: String,
        },
        computed: {
            component() {
                return {
                    template: `<div>${this.content}</div>`,
                    components: {
                        'x-sharp-media': {
                            template: `<ShowFile
                                :field-config-identifier="fieldConfigIdentifier"
                                :value="value"
                                :root="false"
                            />`,
                            components: {
                                ShowFile,
                            },
                            props: {
                                name: String,
                                path: String,
                            },
                            data: () => {
                                return {
                                    fieldConfigIdentifier: this.fieldConfigIdentifier,
                                }
                            },
                            computed: {
                                value() {
                                    return {
                                        name: this.name,
                                        thumbnail: null,
                                    }
                                },
                            },
                        }
                    }
                }
            },
        }
    }
</script>
