<script setup lang="ts">
    import File from "../../File.vue";
    import { inject, ref, useAttrs } from "vue";
    import { FormEditorUploadData } from "@/content/types";
    import { ContentUploadManager } from "@/content/ContentUploadManager";
    import { Show } from "@/show/Show";
    import { ShowFileFieldData } from "@/types";

    const uploadManager = inject<ContentUploadManager<Show>>('uploadManager');
    const upload = uploadManager.getUpload(useAttrs()['data-key'] as string);
</script>

<template>
    <div>
        <File
            class="embed"
            :field="{} as ShowFileFieldData"
            :value="upload.file"
            :locale="null"
            :root="false"
        />
        <template v-if="upload.legend">
            {{ upload.legend }}
        </template>
    </div>
</template>
