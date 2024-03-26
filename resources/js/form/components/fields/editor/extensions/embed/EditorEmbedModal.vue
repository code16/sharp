<script setup lang="ts">
    import { ref } from "vue";
    import { Form } from "@/form/Form";
    import EmbedFormModal from "@/form/components/fields/editor/extensions/embed/EmbedFormModal.vue";
    import { EmbedData, FormData, FormEditorFieldData } from "@/types";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";
    import { Editor } from "@tiptap/vue-3";
    import { useParentForm } from "@/form/useParentForm";

    const props = defineProps<{
        field: FormEditorFieldData,
        editor: Editor
    }>();
    const parentForm = useParentForm();
    const currentModalEmbed = ref<{ id: string, embed: EmbedData, form: Form } | null>(null);
    const embedManager = useParentEditor().embedManager;

    async function postForm(data: EmbedData['value']) {
        const id = await embedManager.postForm(
            currentModalEmbed.value.id,
            currentModalEmbed.value.embed,
            data
        );

        if(!currentModalEmbed.value.id) {
            props.editor.commands.insertEmbed({ id, embed: currentModalEmbed.value.embed });
        }

        currentModalEmbed.value = null;
    }

    function open({ id, embed }: { id?: string, embed:EmbedData }) {
        currentModalEmbed.value = {
            id,
            embed,
            form: new Form({
                    fields: props.field.uploads.fields,
                    layout: props.field.uploads.layout,
                    data: id ? embedManager.getEmbed(id, embed) : {},
                } as FormData,
                parentForm.entityKey,
                parentForm.instanceId,
            ),
        }
    }

    defineExpose({
        open,
    })
</script>

<template>
    <EmbedFormModal
        :visible="!!currentModalEmbed"
        :form="currentModalEmbed?.form"
        :post="postForm"
        @cancel="currentModalEmbed = null"
    >
        <template v-slot:title>
            {{ currentModalEmbed?.embed.label }}
        </template>
    </EmbedFormModal>
</template>
