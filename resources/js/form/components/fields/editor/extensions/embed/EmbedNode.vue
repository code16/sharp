<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import NodeRenderer from "../../NodeRenderer.vue";
    import { Embed, EmbedNodeAttributes } from "@/form/components/fields/editor/extensions/embed/Embed";
    import { computed, onBeforeUnmount, onMounted } from "vue";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem, DropdownMenuSeparator,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { MoreHorizontal } from "lucide-vue-next";

    const props = defineProps<ExtensionNodeProps<typeof Embed, EmbedNodeAttributes>>();

    const embedManager = useParentEditor().embedManager;
    const embedModal = useParentEditor().embedModal;
    const embedData = computed(() => embedManager.getEmbed(props.node.attrs['data-key'], props.extension.options.embed));

    function onRemove() {
        props.deleteNode();
        setTimeout(() => {
            props.editor.commands.focus();
        });
    }

    onMounted(() => {
        embedManager.restoreEmbed(props.node.attrs['data-key'], props.extension.options.embed)
    });

    onBeforeUnmount(() => {
        embedManager.removeEmbed(props.node.attrs['data-key'], props.extension.options.embed);
    });
</script>

<template>
    <NodeRenderer
        class="my-4 first:mt-0 last:mb-0 border rounded-md items-center p-4 flex gap-4"
        :class="{ 'group-focus/editor:border-primary': props.selected }"
        :node="node"
    >
        <div class="flex-1" >
            <template v-if="embedData">
                <div v-html="embedData?._html"></div>
            </template>
            <template v-else>
                {{ extension.options.embed.label }}
            </template>
        </div>
        <DropdownMenu :modal="false">
            <DropdownMenuTrigger as-child>
                <Button class="shrink-0 self-center" variant="ghost" size="icon">
                    <MoreHorizontal class="size-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
                <template v-if="Object.keys(extension.options.embed.fields).length > 0">
                    <DropdownMenuItem @click="embedModal.open({ id: node.attrs['data-key'], embed: extension.options.embed })">
                        {{ __('sharp::form.editor.extension_node.edit_button') }}
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                </template>
                <DropdownMenuItem class="text-destructive" @click="onRemove">
                    {{ __('sharp::form.editor.extension_node.remove_button') }}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </NodeRenderer>
</template>
