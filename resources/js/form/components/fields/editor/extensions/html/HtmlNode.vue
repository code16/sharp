<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { NodeViewWrapper } from '@tiptap/vue-3';
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { Html, HtmlContentNodeAttributes } from "@/form/components/fields/editor/extensions/html/Html";
    import { elementFromString } from "./utils";
    import { ref } from "vue";
    import {
        Dialog,
        DialogClose,
        DialogFooter,
        DialogHeader, DialogScrollContent,
        DialogTitle
    } from "@/components/ui/dialog";
    import { Textarea } from "@/components/ui/textarea";
    import {
        DropdownMenu,
        DropdownMenuContent, DropdownMenuItem,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { MoreHorizontal } from "lucide-vue-next";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";

    const props = defineProps<ExtensionNodeProps<typeof Html, HtmlContentNodeAttributes>>();

    const parentEditor = useParentEditor();
    const modalOpen = ref(props.node.attrs.isNew);
    const editContent = ref('');

    function onEdit() {
        editContent.value = props.node.attrs.content;
        modalOpen.value = true;
    }

    function onRemove() {
        props.deleteNode();
        setTimeout(() => {
            props.editor.commands.focus();
        });
    }

    function onModalOk() {
        const content = elementFromString(editContent.value).innerHTML;
        props.updateAttributes({
            content,
            isNew: false,
        });
        modalOpen.value = false;
    }

    function onModalHidden() {
        if(!props.node.attrs.content) {
            props.deleteNode();
        }
        setTimeout(() => {
            props.editor.commands.focus();
        });
    }
</script>

<template>
    <NodeViewWrapper
        class="my-4 first:mt-0 last:mb-0 border rounded-md items-center p-4 flex gap-4"
        :class="{ 'group-focus/editor:border-primary': selected }"
    >
        <div class="flex-1">
            <pre>{{ node.attrs.content }}</pre>
        </div>
        <template v-if="!parentEditor.props.field.readOnly">
            <DropdownMenu :modal="false">
                <DropdownMenuTrigger as-child>
                    <Button class="shrink-0 self-center" variant="ghost" size="icon">
                        <MoreHorizontal class="size-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                    <DropdownMenuItem @click="onEdit">
                        {{ __('sharp::form.editor.extension_node.edit_button') }}
                    </DropdownMenuItem>
                    <DropdownMenuItem class="text-destructive" @click="onRemove">
                        {{ __('sharp::form.editor.extension_node.remove_button') }}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </template>
        <Dialog
            v-model:open="modalOpen"
            @update:open="!$event && onModalHidden()"
        >
            <DialogScrollContent class="gap-6" @pointer-down-outside.prevent>
                <DialogHeader>
                    <DialogTitle>
                        <template v-if="node.attrs.isNew">
                            {{ __('sharp::form.editor.dialogs.raw_html.insert_title') }}
                        </template>
                        <template v-else>
                            {{ __('sharp::form.editor.dialogs.raw_html.edit_title') }}
                        </template>
                    </DialogTitle>
                </DialogHeader>

                <Textarea v-model="editContent" rows="6" />

                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline">
                            {{ __('sharp::modals.cancel_button') }}
                        </Button>
                    </DialogClose>
                    <Button @click="onModalOk">
                        {{ __('sharp::modals.command.submit_button') }}
                    </Button>
                </DialogFooter>
            </DialogScrollContent>
        </Dialog>
    </NodeViewWrapper>
</template>
