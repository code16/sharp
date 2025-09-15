<script setup lang="ts">
    import { FormEditorFieldData } from "@/types";
    import { nextTick, ref, useTemplateRef } from "vue";
    import { Form } from "@/form/Form";
    import { useParentForm } from "@/form/useParentForm";
    import { FormEditorUploadData } from "@/content/types";
    import { Editor } from "@tiptap/vue-3";
    import { __ } from "@/utils/i18n";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";
    import {
        Dialog,
        DialogClose,
        DialogFooter,
        DialogHeader,
        DialogScrollContent,
        DialogTitle
    } from "@/components/ui/dialog";
    import { Button } from "@/components/ui/button";
    import type FormComponent from "@/form/components/Form.vue";

    const props = defineProps<{
        field: FormEditorFieldData,
        editor: Editor
    }>();
    const input = ref<HTMLInputElement | null>(null);
    const parentForm = useParentForm();
    const uploadManager = useParentEditor().uploadManager;
    const modalOpen = ref(false);
    const modalForm = useTemplateRef<InstanceType<typeof FormComponent>>('modalForm');
    const modalUpload = ref<{ id: string, form: Form, loading?: boolean, locale: string | null } | null>(null);

    async function postForm(data: FormEditorUploadData) {
        modalUpload.value.loading = true;
        const { id } = await uploadManager.postForm(
            modalUpload.value.id,
            modalUpload.value.locale,
            data
        )
            .finally(() => {
                modalUpload.value.loading = false;
            });

        if(modalUpload.value.id == null) {
            props.editor.commands.insertUpload({ id, type: data.file.mime_type });
        }

        modalOpen.value = false;
    }

    function open({ id, locale }: { id?: string, locale?: string | null }) {
        if(props.field.uploads.fields.legend) {
            modalUpload.value = {
                id,
                form: new Form(
                    {
                        fields: props.field.uploads.fields,
                        layout: props.field.uploads.layout,
                        data: id != null ? uploadManager.getUpload(id) : {},
                    },
                    parentForm.entityKey,
                    parentForm.instanceId,
                ),
                locale,
            }
            modalOpen.value = true;
            if(id == null) {
                nextTick(() => {
                    ((modalForm.value!.$el as HTMLElement).querySelector('input[type=file]') as HTMLInputElement).click();
                });
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
    <input
        class="hidden"
        type="file"
        :accept="props.field.uploads.fields.file.allowedExtensions?.join(',')"
        @change="onInputChange"
        ref="input"
    >
    <Dialog v-model:open="modalOpen">
        <DialogScrollContent class="gap-6" @pointer-down-outside.prevent>
            <DialogHeader>
                <DialogTitle>
                    <template v-if="modalUpload.id == null">
                        {{ __('sharp::form.editor.dialogs.upload.title.new') }}

                    </template>
                    <template v-else>
                        {{ __('sharp::form.editor.dialogs.upload.title.update') }}
                    </template>
                </DialogTitle>
            </DialogHeader>

            <SharpForm
                :form="modalUpload.form"
                :post-fn="postForm"
                persist-thumbnail-url
                modal
                ref="modalForm"
            />

            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="outline">
                        {{ __('sharp::modals.cancel_button') }}
                    </Button>
                </DialogClose>
                <Button @click="modalForm!.submit()" :disabled="modalUpload.loading">
                    <template v-if="modalUpload.id == null">
                        {{ __('sharp::form.editor.dialogs.embed.submit_button_insert') }}
                    </template>
                    <template v-else>
                        {{ __('sharp::form.editor.dialogs.embed.submit_button_update') }}
                    </template>
                </Button>
            </DialogFooter>
        </DialogScrollContent>
    </Dialog>
</template>
