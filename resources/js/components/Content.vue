<script setup lang="ts">
    import { router } from "@inertiajs/vue3";
    import { config } from "@/utils/config";
    import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
    import { computed, ref } from "vue";
    import TemplateRenderer from "@/components/TemplateRenderer.vue";

    const props = defineProps<{
        html: string | null,
    }>()

    const hoveringElementWithTitle = ref();
    const updatedHtml = computed(() => {
        return props.html?.replaceAll('title="', 'data-title="');
    });
    const needsTemplate = computed(() => props.html?.includes('<sharp-'));

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
        @mouseover="hoveringElementWithTitle = ($event.target as HTMLElement).closest('[data-title]')"
        @mouseout="hoveringElementWithTitle = null"
    >
        <TooltipProvider>
            <Tooltip :open="!!hoveringElementWithTitle?.dataset.title">
                <TooltipTrigger as-child>
                    <template v-if="needsTemplate">
                        <!-- to handle EL SharpTagsTransformer -->
                        <TemplateRenderer :template="updatedHtml" />
                    </template>
                    <template v-else>
                        <div v-html="updatedHtml"></div>
                    </template>
                </TooltipTrigger>
                <TooltipContent class="max-w-[--reka-tooltip-content-available-width] md:max-w-md" :side-offset="12" :collision-boundary="hoveringElementWithTitle">
                    {{ hoveringElementWithTitle?.dataset.title }}
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    </div>
</template>
