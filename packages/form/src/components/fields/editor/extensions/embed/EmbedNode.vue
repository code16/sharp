<template>
    <NodeRenderer class="editor__node" :node="node">
        <template v-if="!node.attrs.isNew">
            <div class="card">
                <div class="card-body">
                    <TemplateRenderer
                        name="Embed"
                        :template-data="embedData"
                        :template="extension.options.template"
                    />
                    <div class="mt-2">
                        <Button outline small @click="handleEditClicked">
                            {{ lang('form.upload.edit_button') }}
                        </Button>
                        <Button variant="danger" outline small @click="handleRemoveClicked">
                            {{ lang('form.upload.remove_button') }}
                        </Button>
                    </div>
                </div>
            </div>
        </template>
        <EmbedFormModal
            :visible.sync="modalVisible"
            :form="modalForm"
            :post="postForm"
            @cancel="handleCancelClicked"
        >
            <template v-slot:title>
                {{ extension.options.label }}
            </template>
        </EmbedFormModal>
    </NodeRenderer>
</template>

<script>
    import { lang } from "sharp";
    import { Button } from "sharp-ui";
    import { TemplateRenderer } from "sharp/components";
    import NodeRenderer from "../../NodeRenderer";
    import EmbedFormModal from "./EmbedFormModal";
    import { kebabCase } from "./util";

    export default {
        components: {
            EmbedFormModal,
            NodeRenderer,
            TemplateRenderer,
            Button,
        },
        inject: ['$form'],
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
               modalVisible: false,
               modalForm: null,
           }
        },
        computed: {
            embedAttributes() {
                return this.node.attrs.attributes;
            },
            embedData() {
                return {
                    ...this.embedAttributes,
                    ...this.node.attrs.additionalData,
                }
            },
        },
        methods: {
            lang,
            handleEditClicked() {
                this.showForm();
            },
            handleCancelClicked() {
                if(this.node.attrs.isNew) {
                    this.deleteNode();
                    setTimeout(() => {
                        this.editor.commands.focus();
                    }, 0);
                }
            },
            handleRemoveClicked() {
                this.deleteNode();
            },
            async showForm() {
                this.modalForm = await this.extension.options.resolveForm(this.embedData);
                this.modalVisible = true;
            },
            async postForm(data) {
                const attributes = await this.extension.options.postForm(data);
                this.updateAttributes({
                    attributes,
                    additionalData: attributes,
                    isNew: false,
                });
                this.modalVisible = false;
            },
            async init() {
                if(this.node.attrs.isNew) {
                    await this.showForm();
                } else {
                    const additionalData = await this.extension.options.getAdditionalData(this.embedAttributes);
                    if(additionalData) {
                        this.updateAttributes({
                            additionalData,
                        });
                    }
                }
            },
        },
        created() {
            this.init();
        },
    }
</script>
