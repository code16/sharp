<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from "@/components/ui";
    import EmbedRenderer from '@/embeds/components/EmbedRenderer.vue';
    import NodeRenderer from "../../NodeRenderer.vue";
    import EmbedFormModal from "./EmbedFormModal.vue";
    import { Form } from "@/form/Form";
    import { Embed, EmbedNodeAttributes } from "@/form/components/fields/editor/extensions/embed/embed";
    import { nextTick, ref } from "vue";
    import { useParentForm } from "@/form/useParentForm";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";

    const props = defineProps<ExtensionNodeProps<typeof Embed, EmbedNodeAttributes>>();

    const modalVisible = ref(false);
    const embedForm = ref<Form>();
    const parentForm = useParentForm();

    async function showFormModal() {
        const form = await props.extension.options.postResolveForm({
            ...props.node.attrs.embedAttributes,
            ...props.node.attrs.additionalData,
        });
        embedForm.value = new Form(form, parentForm.entityKey, parentForm.instanceId);
        modalVisible.value = true;
    }

    async function postForm(data) {
        const embedAttributes = await props.extension.options.postForm(data, this.modalForm);
        props.updateAttributes({
            embedAttributes,
            additionalData: embedAttributes,
            isNew: false,
        });
        modalVisible.value = false;
    }

    function onCancel() {
        if(props.node.attrs.isNew) {
            props.deleteNode();
            setTimeout(() => {
                props.editor.commands.focus();
            }, 0);
        }
    }

    async function init() {
        if(props.node.attrs.isNew) {
            if(props.extension.options.embed.attributes.length) {
                await showFormModal();
            } else {
                await nextTick();
                props.updateAttributes({
                    isNew: false,
                });
                props.editor.commands.focus();
            }
        } else {
            if(props.extension.options.embed.attributes.length) {
                const additionalData = await props.extension.options.getAdditionalData(props.node.attrs.embedAttributes);
                if(additionalData) {
                    props.updateAttributes({
                        additionalData,
                    });
                }
            }
        }
    }

    init();
</script>

<template>
    <NodeRenderer class="editor__node embed-node" :node="node">
        <template v-if="!node.attrs.isNew">
            <div class="card">
                <div class="card-body">
                    <EmbedRenderer
                        class="embed-node__template"
                        :data="{
                            ...node.attrs.embedAttributes,
                            ...node.attrs.additionalData,
                        }"
                        :options="extension.options"
                    />
                    <div class="mt-3">
                        <div class="row row-cols-auto gx-2">
                            <template v-if="extension.options.embed.attributes.length">
                                <div>
                                    <Button outline small @click="showFormModal()">
                                        {{ __('sharp::form.upload.edit_button') }}
                                    </Button>
                                </div>
                            </template>
                            <div>
                                <Button variant="danger" outline small @click="deleteNode()">
                                    {{ __('sharp::form.upload.remove_button') }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <EmbedFormModal
            v-model:visible="modalVisible"
            :form="embedForm"
            :post="postForm"
            @cancel="onCancel()"
        >
            <template v-slot:title>
                {{ extension.options.embed.label }}
            </template>
        </EmbedFormModal>
    </NodeRenderer>
</template>
