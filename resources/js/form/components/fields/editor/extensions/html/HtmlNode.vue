<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { Modal } from "@/components/ui";
    import { NodeViewWrapper } from '@tiptap/vue-3';
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { Html, HtmlContentNodeAttributes } from "@/form/components/fields/editor/extensions/html/Html";
    import { elementFromString } from "./utils";
    import { ref } from "vue";

    const props = defineProps<ExtensionNodeProps<typeof Html, HtmlContentNodeAttributes>>();

    const modalVisible = ref(false);
    const editContent = ref('');

    function onEdit() {
        editContent.value = props.node.attrs.content;
        modalVisible.value = true;
    }

    function onModalOk() {
        const content = elementFromString(this.editContent).innerHTML;
        props.updateAttributes({
            content,
            isNew: false,
        });
    }

    function onModalHidden() {
        if(!props.node.attrs.content) {
            props.deleteNode();
        }
        setTimeout(() => {
            props.editor.commands.focus();
        });
    }
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
                        <Button variant="secondary" size="sm" @click="onEdit">
                            <i class="fas fa-pencil-alt fs-7"></i>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <Modal
            v-model:visible="modalVisible"
            @ok="onModalOk"
            @hidden="onModalHidden"
        >
            <template v-slot:title>
                <template v-if="node.attrs.isNew">
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
