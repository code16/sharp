<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ShowFileFieldData } from "@/types";
    import { ShowFieldProps } from "../../types";
    import { computed } from "vue";
    import { getClassNameForExtension } from 'font-awesome-filetypes';
    import { filesizeLabel } from '@/utils/file';
    import FieldLayout from "../FieldLayout.vue";
    import { route } from "@/utils/url";
    import { useParentShow } from "@/show/useParentShow";

    const props = defineProps<ShowFieldProps<ShowFileFieldData>>();
    const show = useParentShow();

    const iconClass = computed(() => {
        const extension = (props.value?.name ?? '').split('.').pop();
        const iconClass = getClassNameForExtension(extension);

        if(iconClass === 'fa-file-csv') {
            return `fas ${iconClass}`;
        }
        return `far ${iconClass}`;
    })
</script>

<template>
    <FieldLayout
        class="ShowFileField"
        :class="{
            'ShowFileField--has-label': !!field.label,
            'ShowFileField--has-placeholder': !value?.thumbnail,
        }"
        :label="field.label"
    >
        <div class="row mx-n2">
            <div class="col-3 px-2 align-self-center ShowFileField__thumbnail-col">
                <template v-if="value?.thumbnail">
                    <div class="ShowFileField__thumbnail-container">
                        <img
                            class="ShowFileField__thumbnail max-w-[100px] max-h-[100px]"
                            :src="value.thumbnail"
                            :alt="field.label"
                            ref="thumbnail"
                        >
                    </div>
                </template>
                <template v-else>
                    <div class="ShowFileField__placeholder text-center">
                        <i :class="iconClass"></i>
                    </div>
                </template>
            </div>
            <div class="col px-2" style="min-width: 0">
                <div class="ShowFileField__name text-truncate mb-2">
                    {{ value?.name }}
                </div>
                <div class="ShowFileField__info">
                    <div class="row mx-n2 h-100">
                        <template v-if="value?.size">
                            <div class="col-auto px-2">
                                <div class="ShowFileField__size text-muted">
                                    {{ filesizeLabel(value.size) }}
                                </div>
                            </div>
                        </template>
                        <div class="col-auto px-2">
                            <div class="text-muted">
                                <i class="fa fas fa-download"></i>
                                <a :href="route('code16.sharp.download.show', {
                                        entityKey: show.entityKey,
                                        instanceId: show.instanceId,
                                        disk: value?.disk,
                                        path: value?.path,
                                    })"
                                    :download="value?.name ?? ''"
                                    style="color:inherit"
                                >
                                    {{ __('sharp::show.file.download') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FieldLayout>
</template>
