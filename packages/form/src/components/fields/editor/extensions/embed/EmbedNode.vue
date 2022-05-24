<template>
    <NodeRenderer class="editor__node embed-node" :node="node">
        <template v-if="!node.attrs.isNew">
            <div class="card">
                <div class="card-body">
                    <EmbedRenderer
                        class="embed-node__template"
                        :data="embedData"
                        :options="extension.options"
                    />
                    <div class="mt-3">
                        <div class="row row-cols-auto gx-2">
                            <div>
                                <Button outline small @click="handleEditClicked">
                                    {{ lang('form.upload.edit_button') }}
                                </Button>
                            </div>
                            <div>
                                <Button variant="danger" outline small @click="handleRemoveClicked">
                                    {{ lang('form.upload.remove_button') }}
                                </Button>
                            </div>
                        </div>
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
    import { EmbedRenderer } from 'sharp-embeds';
    import NodeRenderer from "../../NodeRenderer";
    import EmbedFormModal from "./EmbedFormModal";

    export default {
        components: {
            EmbedFormModal,
            NodeRenderer,
            EmbedRenderer,
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
                    ...this.node.attrs.attributes,
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
                    if(this.extension.options.attributes.length > 0) {
                        await this.showForm();
                    } else {
                        this.updateAttributes({
                            isNew: false,
                        });
                    }
                    return;
                }

                const additionalData = await this.extension.options.getAdditionalData(this.node.attrs.attributes);
                if(additionalData) {
                    this.updateAttributes({
                        additionalData,
                    });
                }
            },
        },
        created() {
            this.init();
        },
    }
</script>
