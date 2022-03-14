<template>
    <NodeRenderer class="editor__node" :node="node">
        <div class="card">
            <div class="card-body">
                <TemplateRenderer
                    name="Embed"
                    :template-data="embedData"
                    :template="extension.options.template"
                />
                <Button outline small @click="handleEditClicked">
                    {{ lang('form.upload.edit_button') }}
                </Button>
            </div>
        </div>

    </NodeRenderer>
</template>

<script>
    import { lang } from "sharp";
    import { Button } from "sharp-ui";
    import { TemplateRenderer } from "sharp/components";
    import NodeRenderer from "../../NodeRenderer";

    export default {
        components: {
            NodeRenderer,
            TemplateRenderer,
            Button,
        },
        props: {
            editor: Object,
            node: Object,
            selected: Object,
            extension: Object,
            getPos: Function,
            updateAttributes: Function,
            deleteNode: Function,
        },
        data() {
           return {
               id: null,
           }
        },
        computed: {
            embedData() {
                return this.extension.options.getEmbed(this.id);
            },
        },
        methods: {
            lang,
            handleEditClicked() {

            },
        },
        created() {
            this.id = this.extension.options.registerEmbed(this.node.attrs);
        },
    }
</script>
