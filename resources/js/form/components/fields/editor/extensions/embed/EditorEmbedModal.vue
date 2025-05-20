<script setup lang="ts">
    import { ref, useTemplateRef } from "vue";
    import { Form } from "@/form/Form";
    import { EmbedData, FormEditorFieldData } from "@/types";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";
    import { Editor } from "@tiptap/vue-3";
    import { useParentForm } from "@/form/useParentForm";
    import { __ } from "@/utils/i18n";
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
    const parentForm = useParentForm();
    const embedManager = useParentEditor().embedManager;
    const modalEmbed = ref<{ id?: string, embed: EmbedData, form?: Form, loading?: boolean } | null>(null);
    const modalForm = useTemplateRef<InstanceType<typeof FormComponent>>('modalForm');
    const modalOpen = ref(false);

    async function postForm(data: EmbedData['value']) {
        modalEmbed.value.loading = true;
        const { id } = await embedManager.postForm(
            modalEmbed.value.id,
            modalEmbed.value.embed,
            data
        )
            .finally(() => {
                modalEmbed.value.loading = false;
            });

        modalOpen.value = false;

        if(modalEmbed.value.id == null) {
            props.editor.commands.insertEmbed({ id, embed: modalEmbed.value.embed });
            setTimeout(() => {
                props.editor.commands.focus(props.editor.state.selection.to + 1);
            }, 100);
        }
    }

    async function open({ id, embed }: { id?: string, embed: EmbedData }) {
        if(Object.keys(embed.fields).length > 0) {
            const embedForm = await embedManager.postResolveForm(id, embed);
            modalEmbed.value = {
                id,
                embed,
                form: new Form(embedForm, parentForm.entityKey, parentForm.instanceId, { embedKey: embed.key }),
            }
            modalOpen.value = true;
        } else {
            modalEmbed.value = {
                id,
                embed,
            }
            await postForm(null);
        }
    }

    defineExpose({
        open,
    })
</script>

<template>
    <Dialog v-model:open="modalOpen">
        <DialogScrollContent class="gap-6 max-w-xl" @pointer-down-outside.prevent>
            <DialogHeader>
                <DialogTitle>
                    {{ modalEmbed?.embed.label }}
                </DialogTitle>
            </DialogHeader>

            <SharpForm
                :form="modalEmbed?.form"
                :post-fn="postForm"
                modal
                ref="modalForm"
            />

            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="outline">
                        {{ __('sharp::modals.cancel_button') }}
                    </Button>
                </DialogClose>
                <Button @click="modalForm!.submit()" :disabled="modalEmbed?.loading">
                    <template v-if="modalEmbed.id == null">
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
