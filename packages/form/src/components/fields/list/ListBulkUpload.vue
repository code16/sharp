<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { FormListFieldData, FormUploadFieldData } from "@/types";
    import { ref } from "vue";

    defineProps<{
        field: FormListFieldData,
        currentBulkUploadLimit: number,
        disabled: boolean,
    }>();

    const droppingFile = ref(false);
    const transLink = (text, link?) => {
        return text.replace(/\[(.+?)]\(.*?\)/, link ?? '$1');
    }
</script>

<template>
    <div class="list-group-item text-muted SharpListUpload"
        :class="{
            'SharpListUpload--active': droppingFile,
            'SharpListUpload--disabled': disabled,
        }"
    >
        <div class="SharpListUpload__content d-flex align-items-center justify-content-center">
            <div class="SharpListUpload__text">
                <div class="row align-items-center gx-0">
                    <div class="col-auto">
                        <svg class="SharpListUpload__icon" width="2em" height="2em" viewBox="0 0 32 32">
                            <path d="M26,24v4H6V24H4v4H4a2,2,0,0,0,2,2H26a2,2,0,0,0,2-2h0V24Z"/><polygon points="26 14 24.59 12.59 17 20.17 17 2 15 2 15 20.17 7.41 12.59 6 14 16 24 26 14"/>
                        </svg>
                    </div>
                    <div class="col">
                        <div v-html='transLink(
                                __(`sharp::form.list.bulk_upload.text`),
                                `<a href="#" class="text-reset" tabindex="-1">$1</a>`
                            )'
                            @click.prevent="($refs.input as HTMLInputElement).click()"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="SharpListUpload__help">
            {{ __('sharp::form.list.bulk_upload.help_text', { limit: currentBulkUploadLimit }) }}
        </div>
        <input
            class="SharpListUpload__input"
            type="file"
            :aria-label="transLink(__(`sharp::form.list.bulk_upload.text`))"
            :disabled="disabled"
            :accept="(field.itemFields[field.bulkUploadField] as FormUploadFieldData).fileFilter?.join(',')"
            multiple
            @change="$emit('change', $event)"
            @dragenter="droppingFile = true"
            @dragleave="droppingFile = false"
            @drop="droppingFile = false"
            ref="input"
        >
    </div>
</template>
