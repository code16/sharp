<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { Editor, getMarkRange, getMarkType } from "@tiptap/vue-3";
    import { ref } from "vue";
    import { Input } from "@/components/ui/input";
    import { Label } from "@/components/ui/label";
    import { Toggle } from "@/components/ui/toggle";
    import {  FileCode, ChevronDown } from "lucide-vue-next";
    import { Popover, PopoverAnchor, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import { useId } from "@/composables/useId";
    import { FormFieldProps } from "@/form/types";
    import { FormEditorFieldData } from "@/types";

    const props = defineProps<FormFieldProps<FormEditorFieldData> & {
        editor: Editor,
    }>();

    const open = ref(false);
    const language = ref<string | null>(null);

    props.editor.on('transaction', () => {
        language.value = props.editor.getAttributes('codeBlock')?.language;
    });

    function onOpen() {
    }

    function onHide() {
        props.editor.chain()
            .focus()
            .run();
    }

    function onSubmit() {
        props.editor.chain().focus().updateAttributes('codeBlock', { language: language.value?.toLowerCase() }).run();
    }

    function onToggleClick() {
        if(props.editor.isActive('codeBlock')) {
            open.value = true;
        } else {
            props.editor.chain().focus().toggleCodeBlock().run();
        }
    }

    function onUntoggleCodeBlockClick() {
        if(props.editor.isActive('codeBlock')) {
            props.editor.chain().focus().toggleCodeBlock().run();
        } else {
            props.editor.chain().focus().run();
        }
    }

    defineExpose({
        open,
    });
</script>

<template>
    <Popover
        v-model:open="open"
        @update:open="$event ? onOpen() : onHide()"
        :modal="false"
    >
        <PopoverAnchor as-child>
            <Toggle
                :model-value="open || props.editor.isActive('codeBlock')"
                size="sm"
                :disabled="props.field.readOnly"
                :title="__('sharp::form.editor.toolbar.code_block.title')"
                @click="onToggleClick"
            >
                <FileCode class="size-4" />
            </Toggle>
        </PopoverAnchor>

        <PopoverContent class="w-max">
            <form @submit.prevent="onSubmit()">
                <div class="flex gap-3 ">
                    <Input class="flex-1 min-w-0 w-24" v-model="language" :placeholder="__('sharp::form.editor.dialogs.code_block.language_label')" />
                    <Button type="submit">{{ __('sharp::modals.ok_button') }}</Button>
                </div>
            </form>
            <Button class="mt-3 w-full" variant="outline" size="sm" @click="onUntoggleCodeBlockClick">
                {{ __('sharp::form.editor.dialogs.code_block.toggle_off') }}
            </Button>
        </PopoverContent>
    </Popover>
</template>
