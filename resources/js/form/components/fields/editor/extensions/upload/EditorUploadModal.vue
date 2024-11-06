<script setup lang="ts">
    import { FormFieldProps } from "@/form/types";
    import { FormData, FormEditorFieldData } from "@/types";
    import { nextTick, ref } from "vue";
    import { Form } from "@/form/Form";
    import { useParentForm } from "@/form/useParentForm";
    import { FormEditorUploadData } from "@/content/types";
    import { Editor } from "@tiptap/vue-3";
    import { __ } from "@/utils/i18n";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";

    const props = defineProps<{
        field: FormEditorFieldData,
        editor: Editor
    }>();
    const input = ref<HTMLInputElement | null>(null);
    const parentForm = useParentForm();
    const currentModalUpload = ref<{ id: string, form: Form } | null>(null);
    const uploadManager = useParentEditor().uploadManager;

    async function postForm(data: FormEditorUploadData) {
        const { id } = await uploadManager.postForm(currentModalUpload.value.id, data);

        if(!currentModalUpload.value.id) {
            props.editor.commands.insertUpload({ id, type: data.file.mime_type });
        }

        currentModalUpload.value = null;
    }

    function open(id?: string) {
        if(props.field.uploads.fields.legend) {
            currentModalUpload.value = {
                id,
                form: new Form(
                    {
                        fields: props.field.uploads.fields,
                        layout: props.field.uploads.layout,
                        data: id ? uploadManager.getUpload(id) : {},
                    },
                    parentForm.entityKey,
                    parentForm.instanceId,
                ),
            }
        } else {
            input.value.click();
        }
    }

    function onInputChange(e: Event & { target: HTMLInputElement }) {
        props.editor.commands.insertUpload({ nativeFile: e.target.files[0] });
        e.target.value = '';
    }

    defineExpose({
        open,
    });
</script>

<template>
    <input class="hidden" type="file" :accept="props.field.uploads.fields.file.allowedExtensions.join(',')" @change="onInputChange" ref="input">

<!--    <EmbedFormModal-->
<!--        :visible="!!currentModalUpload"-->
<!--        :form="currentModalUpload?.form"-->
<!--        :post="postForm"-->
<!--        @cancel="currentModalUpload = null"-->
<!--    >-->
<!--        <template v-slot:title>-->
<!--            <template v-if="currentModalUpload?.id">-->
<!--                {{ __('sharp::form.editor.dialogs.upload.title.new') }}-->
<!--            </template>-->
<!--            <template v-else>-->
<!--                {{ __('sharp::form.editor.dialogs.upload.title.update') }}-->
<!--            </template>-->
<!--        </template>-->
<!--    </EmbedFormModal>-->
</template>
