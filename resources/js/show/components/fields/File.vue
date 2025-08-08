<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ShowFileFieldData } from "@/types";
    import { ShowFieldProps } from "../../types";
    import { filesizeLabel } from '@/utils/file';
    import { route } from "@/utils/url";
    import { useParentShow } from "@/show/useParentShow";
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { Button } from "@/components/ui/button";
    import { MoreHorizontal } from "lucide-vue-next";
    import ShowFieldLayout from "@/show/components/ShowFieldLayout.vue";
    import FileIcon from "@/components/FileIcon.vue";
    import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";

    const props = defineProps<ShowFieldProps<ShowFileFieldData> & { legend?: string }>();
    const show = useParentShow();
</script>

<template>
    <ShowFieldLayout v-bind="props">
        <div class="flex gap-4 p-4 border rounded-md max-w-[600px]">
            <template v-if="value">
                <template v-if="value.thumbnail">
                    <div class="self-center">
                        <img class="rounded-sm min-w-[40px] max-w-[100px] max-h-[100px] object-contain"
                            :src="value.thumbnail"
                            :alt="field.label"
                        >
                    </div>
                </template>
                <template v-else>
                    <FileIcon class="self-center size-4" :mime-type="value.mime_type" />
                </template>
                <div class="flex-1 min-w-0 flex flex-col">
                    <div class="truncate text-sm font-medium">
                        <TooltipProvider>
                            <Tooltip :delay-duration="0" disable-hoverable-content>
                                <TooltipTrigger as-child>
                                    <a class="text-foreground underline underline-offset-4 decoration-foreground/20 hover:underline hover:decoration-foreground"
                                        :href="route('code16.sharp.download.show', {
                                            entityKey: show.entityKey,
                                            instanceId: show.instanceId,
                                            disk: value.disk,
                                            path: value.path,
                                        })"
                                        :download="value.name ?? ''"
                                    >
                                        {{ value.name }}
                                    </a>
                                </TooltipTrigger>

                                <TooltipContent class="pointer-events-none" :side-offset="10">
                                    {{ __('sharp::form.upload.download_tooltip') }}
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                    <template v-if="legend">
                        <div class="mt-2 text-muted-foreground text-sm">
                            {{ legend }}
                        </div>
                    </template>
                    <div class="pt-2 flex gap-4 text-sm">
                        <template v-if="value.size">
                            <div class="text-xs text-muted-foreground">
                                {{ filesizeLabel(value.size) }}
                            </div>
                        </template>
                    </div>
                </div>
                <DropdownMenu :modal="false">
                    <DropdownMenuTrigger as-child>
                        <Button class="self-center" variant="ghost" size="icon">
                            <MoreHorizontal class="size-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DropdownMenuItem
                            as="a"
                            :download="value.name ?? ''"
                            :href="route('code16.sharp.download.show', {
                                entityKey: show.entityKey,
                                instanceId: show.instanceId,
                                disk: value.disk,
                                path: value.path,
                            })"
                        >
                            {{ __('sharp::show.file.download') }}
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </template>
        </div>
    </ShowFieldLayout>
</template>
