<script setup lang="ts">
    import { FormEditorFieldData } from "@/types";
    import { FormFieldProps } from "@/form/types";
    import { Editor } from "@tiptap/vue-3";
    import { __ } from "@/utils/i18n";
    import type { HTMLAttributes } from "vue";
    import { cn } from "@/utils/cn";

    const props = defineProps<FormFieldProps<FormEditorFieldData> & {
        editor: Editor,
        class?: HTMLAttributes['class'],
    }>();
</script>

<template>
    <template v-if="field.maxLength">
        <div :class="cn(props.class, { 'text-destructive': editor.storage.characterCount.characters() > field.maxLength })">
            {{ __('sharp::form.editor.character_count', { count: `${editor.storage.characterCount.characters()} / ${field.maxLength}` }) }}
        </div>
    </template>
    <template v-else>
        <div :class="props.class">
            {{ __('sharp::form.editor.character_count', { count: editor.storage.characterCount.characters() }) }}
        </div>
    </template>
</template>
