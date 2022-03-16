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
            embedData() {
                return {
                    ...this.embedAttributes,
                    ...this.node.attrs.additionalData,
                }
            },
            embedAttributes() {
                return Object.fromEntries(
                    Object.entries(this.node.attrs)
                        .filter(([name]) =>
                            this.extension.options.attributes.includes(name)
                        )
                );
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
                this.modalForm = await this.extension.options.resolveForm(this.embedAttributes);
                this.modalVisible = true;
            },
            async postForm(data) {
                const attributes = await this.extension.options.postForm(data);
                this.modalVisible = false;
                this.updateAttributes({
                    ...attributes,
                    isNew: false,
                    additionalData: {
                        ...attributes
                    },
                });
            },
            async init() {
                if(this.node.attrs.isNew) {
                    await this.showForm();
                } else {
                    const data = await this.extension.options.getAdditionalData(this.embedAttributes);
                    this.updateAttributes({
                        additionalData: data,
                    });
                }
            },
        },
        created() {
            this.init();
        },
    }
</script>
