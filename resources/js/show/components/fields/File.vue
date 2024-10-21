<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ShowFileFieldData } from "@/types";
    import { ShowFieldProps } from "../../types";
    import { filesizeLabel } from '@/utils/file';
    import { route } from "@/utils/url";
    import { useParentShow } from "@/show/useParentShow";
    import { Card, CardContent } from "@/components/ui/card";

    const props = defineProps<ShowFieldProps<ShowFileFieldData> & { legend?: string }>();
    const show = useParentShow();
</script>

<template>
    <div class="flex gap-4 border rounded-md p-4 max-w-[500px]">
        <template v-if="value">
            <template v-if="value.thumbnail">
                <img class="rounded-sm max-w-[100px] max-h-[100px]"
                    :src="value.thumbnail"
                    :alt="field.label"
                >
            </template>
<!--            <template v-else>-->
<!--                <FileIcon class="size-6" :mime-type="value.mime_type" />-->
<!--            </template>-->
            <div class="flex-1 flex flex-col">
                <div class="truncate text-sm font-medium">{{ value.name }}</div>
                <template v-if="legend">
                    <div class="mt-2 text-muted-foreground text-sm">
                        {{ legend }}
                    </div>
                </template>
                <div class="mt-auto pt-2 flex gap-4 text-sm">
                    <template v-if="value.size">
                        <div class="text-sm text-muted-foreground">
                            {{ filesizeLabel(value.size) }}
                        </div>
                    </template>
                    <a :href="route('code16.sharp.download.show', {
                            entityKey: show.entityKey,
                            instanceId: show.instanceId,
                            disk: value.disk,
                            path: value.path,
                        })"
                        :download="value.name ?? ''"
                        class="ml-auto relative underline text-sm text-muted-foreground decoration-1 underline-offset-2 hover:text-foreground"
                    >
                        <span class="absolute -inset-2"></span>
                        {{ __('sharp::show.file.download') }}
                    </a>
                </div>
            </div>
        </template>
    </div>
</template>
