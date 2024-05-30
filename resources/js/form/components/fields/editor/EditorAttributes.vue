<script setup lang="ts">
    import { Editor } from "@tiptap/vue-3";
    import { StyleValue, useAttrs, watchEffect } from "vue";
    import { normalizeStyle, stringifyStyle } from "@vue/shared";

    const props = defineProps<{
        editor: Editor,
        class: string,
        style: StyleValue,
    }>();

    const attrs = useAttrs();

    watchEffect(() => {
        props.editor.setOptions({
            editorProps: {
                attributes: {
                    class: props.class,
                    style: stringifyStyle(normalizeStyle(props.style)),
                },
            }
        });
    });
</script>

<template>
    <slot />
</template>
