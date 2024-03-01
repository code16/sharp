<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from "@/components/ui";
    import EmbedRenderer from '@/content/components/EmbedRenderer.vue';
    import NodeRenderer from "../../NodeRenderer.vue";
    import EmbedFormModal from "./EmbedFormModal.vue";
    import { Form } from "@/form/Form";
    import { Embed, EmbedNodeAttributes } from "@/form/components/fields/editor/extensions/embed/Embed";
    import { nextTick, onUnmounted, ref } from "vue";
    import { useParentForm } from "@/form/useParentForm";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { EmbedData } from "@/types";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";

    const props = defineProps<ExtensionNodeProps<typeof Embed, EmbedNodeAttributes>>();

    const modalVisible = ref(false);
    const embedForm = ref<Form>();
    const parentForm = useParentForm();
    const embedManager = useParentEditor().embedManager;

    async function showFormModal() {
        const form = await embedManager.postResolveForm(
            props.node.attrs['data-unique-id'],
            props.extension.options.embed,
        );
        embedForm.value = new Form(form, parentForm.entityKey, parentForm.instanceId);
        modalVisible.value = true;
    }

    async function postForm(data: EmbedData['value']) {
        const responseData = await embedManager.postForm(
            props.node.attrs['data-unique-id'],
            props.extension.options.embed,
            data,
        );

        props.updateAttributes({
            embedAttributes: responseData,
            isNew: false,
        });

        modalVisible.value = false;
    }

    function onCancel() {
        modalVisible.value = false;
        if(props.node.attrs.isNew) {
            props.deleteNode();
            setTimeout(() => {
                props.editor.commands.focus();
            }, 0);
        }
    }

    function onRemove() {
        embedManager.removeEmbed(props.node.attrs['data-unique-id']);
        props.deleteNode();
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
                const additionalData = await embedManager.getResolvedEmbed(
                    props.node.attrs['data-unique-id']
                );
                if(additionalData) {
                    props.updateAttributes({
                        additionalData,
                    });
                }
            }
        }
    }

    onUnmounted(() => {
        embedManager.removeEmbed(props.node.attrs['data-unique-id']);
    });

    init();
</script>

<template>
    <NodeRenderer class="editor__node embed-node" :node="node">
        <div>
            <template v-if="!node.attrs.isNew">
                <div class="card">
                    <div class="card-body">
                        <EmbedRenderer
                            class="embed-node__template"
                            :data="{
                                ...node.attrs.embedAttributes,
                                ...node.attrs.additionalData,
                            }"
                            :embed="extension.options.embed"
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
                                    <Button variant="danger" outline small @click="onRemove">
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
                @cancel="onCancel"
            >
                <template v-slot:title>
                    {{ extension.options.embed.label }}
                </template>
            </EmbedFormModal>
        </div>
    </NodeRenderer>
</template>
