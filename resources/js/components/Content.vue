<script setup lang="ts">
    import { router } from "@inertiajs/vue3";
    import { config } from "@/utils/config";
    import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
    import { computed, ref } from "vue";

    const props = defineProps<{
        html: string | null,
    }>()

    const hoveringLink = ref();
    const updatedHtml = computed(() => {
        return props.html?.replaceAll('title="', 'data-title="');
    });

    function onClick(e: MouseEvent & { target: HTMLElement }) {
        const link: HTMLAnchorElement = e.target.closest('a[href]');
        const base = `${location.origin}/${config('sharp.custom_url_segment')}/`;

        if(link?.href.startsWith(base)) {
            router.visit(link.href);
            e.preventDefault();
        }
    }
</script>

<template>
    <div class="content"
        @click="onClick"
        @mouseover="hoveringLink = ($event.target as HTMLElement).closest('a')"
        @mouseout="hoveringLink = null"
    >
        <TooltipProvider>
            <Tooltip :open="!!hoveringLink?.dataset.title">
                <TooltipTrigger as-child>
                    <div v-html="updatedHtml"></div>
                </TooltipTrigger>
                <TooltipContent :side-offset="12" :collision-boundary="hoveringLink">
                    {{ hoveringLink?.dataset.title }}
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    </div>
</template>
