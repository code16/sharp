<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { Iframe, IframeAttributes } from "@/form/components/fields/editor/extensions/iframe/Iframe";
    import debounce from 'lodash/debounce';
    import { Button } from '@/components/ui/button';
    import NodeRenderer from "../../NodeRenderer.vue";
    import { getHTMLFromFragment } from "@tiptap/core";
    import { Fragment, Node } from "@tiptap/pm/model";
    import { ref } from "vue";
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuSeparator, DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import {
        Dialog,
        DialogClose,
        DialogFooter,
        DialogHeader,
        DialogScrollContent,
        DialogTitle
    } from "@/components/ui/dialog";
    import { Textarea } from "@/components/ui/textarea";
    import { MoreHorizontal } from "lucide-vue-next";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";

    const props = defineProps<ExtensionNodeProps<typeof Iframe, IframeAttributes>>();

    const parentEditor = useParentEditor();
    const modalOpen = ref(props.node.attrs.isNew);
    const html = ref<string>();
    const previewHtml = ref<string>();
    const invalid = ref(false);

    function getIframe(html: string) {
        const dom = document.createElement('div');
        dom.innerHTML = html;
        return dom.querySelector('iframe');
    }

    function onEdit() {
        const rendered = getHTMLFromFragment(Fragment.from(props.node as Node), props.editor.schema);
        html.value = getIframe(rendered).outerHTML;
        previewHtml.value = html.value;
        modalOpen.value = true;
        invalid.value = false;
    }

    function onRemove() {
        props.deleteNode();
        setTimeout(() => {
            props.editor.commands.focus();
        });
    }

    function onModalOk(e) {
        const iframe = getIframe(html.value);
        if(iframe) {
            props.updateAttributes({
                attributes: Object.fromEntries(
                    [...iframe.attributes].map(attr => [attr.name, attr.value])
                ),
                isNew: false,
            });
            modalOpen.value = false;
            setTimeout(() => props.editor.commands.focus(props.getPos() + 1));
        } else {
            invalid.value = true;
        }
    }

    function onModalHidden() {
        if(props.node.attrs.isNew) {
            props.deleteNode();
            setTimeout(() => props.editor.commands.focus());
        }
    }

    function onModalInputChange() {
        const iframe = getIframe(html.value);
        invalid.value = !!html.value && !iframe;
        if(iframe) {
            iframe.removeAttribute('style');
            previewHtml.value = iframe.outerHTML;
        } else {
            previewHtml.value = '';
        }
    }
    const debouncedOnModalInputChange = debounce(onModalInputChange, 200);
</script>

<template>
    <NodeRenderer
        class="my-4 first:mt-0 last:mb-0 border rounded-md items-center p-4 flex gap-4"
        :class="{ 'group-focus/editor:border-primary': props.selected }"
        :node="node"
    >
        <div class="flex-1 min-w-0">
            <template v-if="!node.attrs.isNew">
                <iframe class="w-full max-h-[200px] [[height$='%']]:h-[200px]" v-bind="node.attrs.attributes"></iframe>
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
                    <DropdownMenuItem @click="onEdit()">
                        {{ __('sharp::form.editor.extension_node.edit_button') }}
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem class="text-destructive" @click="onRemove()">
                        {{ __('sharp::form.editor.extension_node.remove_button') }}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </template>

        <Dialog
            v-model:open="modalOpen"
            @update:open="!$event && onModalHidden()"
        >
            <DialogScrollContent class="gap-6 max-w-xl" @pointer-down-outside.prevent>
                <DialogHeader>
                    <DialogTitle>
                        <template v-if="node.attrs.isNew">
                            {{ __('sharp::form.editor.dialogs.iframe.insert_title') }}
                        </template>
                        <template v-else>
                            {{ __('sharp::form.editor.dialogs.iframe.update_title') }}
                        </template>
                    </DialogTitle>
                </DialogHeader>

                <div>
                    <Textarea
                        placeholder="&lt;iframe src=&quot;...&quot;&gt;&lt;/iframe&gt;"
                        v-model="html"
                        rows="6"
                        :aria-label="__('sharp::form.editor.dialogs.iframe.insert_title')"
                        @update:model-value="debouncedOnModalInputChange"
                        @paste="onModalInputChange"
                        @focus="($event.target as HTMLTextAreaElement).select()"
                    />
                    <template v-if="invalid">
                        <div class="mt-2 text-destructive text-sm">
                            {{ __('sharp::form.editor.dialogs.iframe.invalid_message') }}
                        </div>
                    </template>

                    <template v-if="previewHtml && !invalid">
                        <div class="mt-4 [&_iframe]:w-full [&_iframe]:max-h-[260px] [&_iframe[height$='%']]:h-[260px]" v-html="previewHtml">
                        </div>
                    </template>
                </div>

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
    </NodeRenderer>
</template>
