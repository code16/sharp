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
    import EmbedHeader from "@/components/EmbedHeader.vue";
    import { useEditorNode } from "@/form/components/fields/editor/useEditorNode";

    const props = defineProps<ExtensionNodeProps<typeof Embed, EmbedNodeAttributes>>();

    const parentEditor = useParentEditor();
    const embedManager = useParentEditor().embedManager;
    const embedModal = useParentEditor().embedModal;
    const embedData = computed(() => embedManager.getEmbed(props.node.attrs['data-key'], props.extension.options.embed));

    function onRemove() {
        props.deleteNode();
        setTimeout(() => {
            props.editor.commands.focus();
        });
    }

    function onCopy() {
        props.editor.commands.setNodeSelection(props.getPos());
        const clipboardData = new DataTransfer();
        const event = new ClipboardEvent('copy', {
            bubbles: true,
            cancelable: true,
            clipboardData,
        });

        props.editor.view.dom.dispatchEvent(event);

        const clipboardItem = new ClipboardItem({ 'text/html': clipboardData.getData('text/html') });

        navigator.clipboard.write([clipboardItem]).then(() => {
        }).catch(err => {
            alert(__('sharp::errors.failed_to_write_to_clipboard'));
        });
    }

    useEditorNode({
        onAdded: () => {
            embedManager.restoreEmbed(props.node.attrs['data-key'], props.extension.options.embed)
        },
        onRemoved: () => {
            embedManager.removeEmbed(props.node.attrs['data-key'], props.extension.options.embed);
        },
    });
</script>

<template>
    <NodeRenderer
        class="my-4 first:mt-0 last:mb-0 border rounded-md items-center p-4 flex gap-4 group-focus/editor:data-[textselected]:border-primary"
        :class="{ 'group-focus/editor:border-primary': props.selected }"
        :node="node"
    >
        <div class="flex-1 min-w-0">
            <template v-if="extension.options.embed.displayEmbedHeader">
                <EmbedHeader :embed="extension.options.embed" />
            </template>

            <template v-if="embedData?._html">
                <div v-html="embedData?._html"></div>
            </template>
        </div>
        <template v-if="!parentEditor.props.field.readOnly">
            <DropdownMenu :modal="false">
                <DropdownMenuTrigger as-child>
                    <Button class="shrink-0 self-center" variant="ghost" size="icon" :aria-label="__('sharp::form.editor.extension_node.dropdown_button.aria_label')">
                        <MoreHorizontal class="size-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                    <template v-if="Object.keys(extension.options.embed.fields).length > 0">
                        <DropdownMenuItem @click="embedModal.open({ id: node.attrs['data-key'], embed: extension.options.embed })">
                            {{ __('sharp::form.editor.extension_node.edit_button') }}
                        </DropdownMenuItem>
                    </template>
                    <DropdownMenuItem @click="onCopy">
                        {{ __('sharp::form.editor.extension_node.copy_button') }}
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem class="text-destructive" @click="onRemove">
                        {{ __('sharp::form.editor.extension_node.remove_button') }}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </template>
    </NodeRenderer>
</template>
