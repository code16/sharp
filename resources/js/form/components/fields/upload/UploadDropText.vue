<script setup lang="ts">
    import { __ } from "@/utils/i18n";

    const text = __('sharp::form.list.bulk_upload.text')
        .match(/(.*)\[(.+?)]\(\)(.*)/);
</script>

<template>
    {{ text[1] }}
    <slot name="link" :text="text[2]" />
    {{ text[3] }}
</template>
