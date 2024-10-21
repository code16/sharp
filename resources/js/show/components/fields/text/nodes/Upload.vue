<script setup lang="ts">
    import File from "../../File.vue";
    import { inject, ref, useAttrs } from "vue";
    import { ContentUploadManager } from "@/content/ContentUploadManager";
    import { Show } from "@/show/Show";
    import { ShowFileFieldData } from "@/types";

    const uploadManager = inject<ContentUploadManager<Show>>('uploadManager');
    const upload = uploadManager.getUpload(useAttrs()['data-key'] as string);
</script>

<template>
    <File
        class="my-4 first:mt-0 last:mb-0"
        :field="{} as ShowFileFieldData"
        :value="upload.file"
        :legend="upload.legend"
        :locale="null"
        :root="false"
    />
</template>
