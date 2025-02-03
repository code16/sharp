<script setup lang="ts">
    import { router } from "@inertiajs/vue3";
    import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
    import { computed, ref } from "vue";
    import TemplateRenderer from "@/components/TemplateRenderer.vue";
    import { isSharpLink } from "@/utils/url";

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

        if(link && isSharpLink(link.href)) {
            router.visit(link.href);
            e.preventDefault();
        }
    }
</script>

<template>
    <div
        @click="onClick"
        @mouseover="hoveringElementWithTitle = ($event.target as HTMLElement).closest('[data-title]')"
        @mouseout="hoveringElementWithTitle = null"
    >
        <TooltipProvider>
            <Tooltip :open="!!hoveringElementWithTitle?.dataset.title">
                <TooltipTrigger as-child>
                    <template v-if="needsTemplate">
                        <!-- to handle EL SharpTagsTransformer -->
                        <TemplateRenderer class="content" :template="updatedHtml" />
                    </template>
                    <template v-else>
                        <div class="content" v-html="updatedHtml"></div>
                    </template>
                </TooltipTrigger>
                <TooltipContent class="max-w-[--reka-tooltip-content-available-width] md:max-w-md" :side-offset="12" :collision-boundary="hoveringElementWithTitle">
                    {{ hoveringElementWithTitle?.dataset.title }}
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    </div>
</template>
