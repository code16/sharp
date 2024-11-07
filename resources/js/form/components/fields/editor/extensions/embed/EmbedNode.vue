<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import NodeRenderer from "../../NodeRenderer.vue";
    import { Embed, EmbedNodeAttributes } from "@/form/components/fields/editor/extensions/embed/Embed";
    import { computed, onMounted, onUnmounted } from "vue";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { MoreHorizontal } from "lucide-vue-next";

    const props = defineProps<ExtensionNodeProps<typeof Embed, EmbedNodeAttributes>>();

    const embedManager = useParentEditor().embedManager;
    const embedModal = useParentEditor().embedModal;
    const embedData = computed(() => embedManager.getEmbed(props.node.attrs['data-key'], props.extension.options.embed));

    onMounted(() => {
        embedManager.restoreEmbed(props.node.attrs['data-key'], props.extension.options.embed)
    });

    onUnmounted(() => {
        embedManager.removeEmbed(props.node.attrs['data-key'], props.extension.options.embed);
    });
</script>

<template>
    <NodeRenderer
        class="my-4 first:mt-0 last:mb-0 border rounded-md items-center p-4 flex"
        :class="{ 'group-focus/editor:border-primary': selected }"
        :node="node"
    >
        <div class="flex-1" v-html="embedData._html">
        </div>
        <DropdownMenu :modal="false">
            <DropdownMenuTrigger as-child>
                <Button class="self-center" variant="ghost" size="icon">
                    <MoreHorizontal class="size-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
                <DropdownMenuItem @click="embedModal.open({ id: node.attrs['data-key'], embed: extension.options.embed })">
                    {{ __('sharp::form.upload.edit_button') }}
                </DropdownMenuItem>
                <DropdownMenuItem class="text-destructive" @click="deleteNode()">
                    {{ __('sharp::form.upload.remove_button') }}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </NodeRenderer>
</template>
