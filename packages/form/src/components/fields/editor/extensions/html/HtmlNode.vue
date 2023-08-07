<script setup lang="ts">
    import { __ } from "@/utils/i18n";
</script>

<template>
    <NodeViewWrapper>
        <div class="card editor__node html-node" :class="{ 'shadow border-primary': selected }">
            <div class="card-body">
                <div class="row">
                    <div class="col" style="min-width: 0">
                        <pre class="mb-0">{{ node.attrs.content }}</pre>
                    </div>
                    <div class="col-auto me-n2 my-n2">
                        <Button small variant="light" @click="handleEditClicked">
                            <i class="fas fa-pencil-alt fs-7"></i>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <Modal
            v-model:visible="editVisible"
            @ok="handleModalOk"
            @hidden="handleModalHidden"
            @shown="handleModalShown"
            ref="modal"
        >
            <template v-slot:title>
                <template v-if="node.attrs.new">
                    {{ __('sharp::form.editor.dialogs.raw_html.insert_title') }}
                </template>
                <template v-else>
                    {{ __('sharp::form.editor.dialogs.raw_html.edit_title') }}
                </template>
            </template>

            <textarea class="form-control" v-model="editContent" rows="6"></textarea>
        </Modal>
    </NodeViewWrapper>
</template>

<script lang="ts">
    import { Button, Modal } from "@sharp/ui";
    import { NodeViewWrapper } from '@tiptap/vue-3';
    import { elementFromString } from "./util";

    export default {
        components: {
            NodeViewWrapper,
            Button,
            Modal
        },
        props: {
            node: Object,
            editor: Object,
            updateAttributes: Function,
            selected: Boolean,
            deleteNode: Function,
        },
        data() {
            return {
                editContent: null,
                editVisible: this.node.attrs.new,
            }
        },
        methods: {
            handleEditClicked() {
                this.editContent = this.node.attrs.content;
                this.$refs.modal.show();
            },
            handleModalOk() {
                const content = elementFromString(this.editContent).innerHTML;
                this.updateAttributes({
                    content,
                    new: false,
                });
            },
            handleModalShown(e) {
                e.target.querySelector('textarea').focus();
            },
            handleModalHidden() {
                if(!this.node.attrs.content) {
                    this.deleteNode();
                }
                setTimeout(() => {
                    this.editor.commands.focus();
                });
            },
        },
    }
</script>
