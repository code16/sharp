<script setup lang="ts">

    import { computed } from "vue";
    import {
        File,
        FileArchive,
        FileAudio,
        FilePieChart,
        FileSpreadsheet,
        FileText,
        FileType,
        FileVideo
    } from "lucide-vue-next";

    const props = defineProps<{
        mimeType: string,
    }>();

    const iconComponent = computed(() => {
        const mimeType = props.mimeType ?? '';
        if(mimeType.startsWith('audio/')) {
            return FileAudio;
        } else if(mimeType.startsWith('video/')) {
            return FileVideo;
        } else if(
            mimeType.startsWith('application/vnd.ms-excel')
            || mimeType.startsWith('application/vnd.openxmlformats-officedocument.spreadsheetml')
            || mimeType === 'text/csv'
        ) {
            return FileSpreadsheet;
        } else if(mimeType.startsWith('application/vnd.ms-powerpoint') || mimeType.startsWith('application/vnd.openxmlformats-officedocument.presentationml')) {
            return FilePieChart;
        } else if(mimeType.startsWith('application/msword') || mimeType.startsWith('application/vnd.openxmlformats-officedocument.wordprocessingml')) {
            return FileType;
        } else if(mimeType.startsWith('text/') || mimeType === 'application/pdf') {
            return FileText;
        } else if(
            mimeType === 'application/zip'
            || mimeType === 'application/gzip'
            || mimeType === 'application/vnd.rar'
            || mimeType === 'application/x-7z-compressed'
        ) {
            return FileArchive;
        }
        else {
            return File;
        }
    })
</script>

<template>
    <component :is="iconComponent" />
</template>

