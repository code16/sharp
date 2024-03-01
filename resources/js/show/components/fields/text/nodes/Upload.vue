<script setup lang="ts">
    import File from "../../File.vue";
    import { inject, ref, useAttrs } from "vue";
    import { FormEditorUploadData } from "@/content/types";
    import { ContentUploadManager } from "@/content/ContentUploadManager";
    import { Show } from "@/show/Show";
    import { ShowFileFieldData } from "@/types";

    const value = ref<FormEditorUploadData>();
    const uploadManager = inject<ContentUploadManager<Show>>('uploadManager');

    async function init() {
        value.value = await uploadManager.getResolvedUpload(useAttrs()['data-unique-id'] as string);
    }

    init();
</script>

<template>
    <File
        v-if="value"
        class="embed"
        :field="{} as ShowFileFieldData"
        :value="value.file"
        :locale="null"
        :root="false"
    />
    <template v-if="value.legend">
        {{ value.legend }}
    </template>
</template>
