<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { Iframe, IframeAttributes } from "@/form/components/fields/editor/extensions/iframe/Iframe";
    import debounce from 'lodash/debounce';
    import { Modal, Button } from "@/components/ui";
    import NodeRenderer from "../../NodeRenderer.vue";
    import { getHTMLFromFragment } from "@tiptap/core";
    import { Fragment, Node } from "@tiptap/pm/model";
    import { ref } from "vue";

    const props = defineProps<ExtensionNodeProps<typeof Iframe, IframeAttributes>>();

    const modalVisible = ref(props.node.attrs.isNew);
    const html = ref<string>();
    const previewHtml = ref<string>();
    const invalid = ref(false);

    function getIframe(html) {
        const dom = document.createElement('div');
        dom.innerHTML = html;
        return dom.querySelector('iframe');
    }

    function onEdit() {
        const rendered = getHTMLFromFragment(Fragment.from(props.node as Node), props.editor.schema);
        html.value = getIframe(rendered).outerHTML;
        previewHtml.value = html.value;
        modalVisible.value = true;
        invalid.value = false;
    }

    function onRemove() {
        props.deleteNode();
    }

    function onModalOk(e) {
        const iframe = getIframe(html.value);
        if(iframe) {
            props.updateAttributes({
                ...Object.fromEntries(
                    Object.entries(props.node.type.spec.attrs)
                        .map(([attr, spec]) => [attr, spec.default])
                ),
                ...Object.fromEntries(
                    [...iframe.attributes].map(attr => [attr.name, attr.value])
                ),
                isNew: false,
            });
            modalVisible.value = false;
        } else {
            e.preventDefault();
        }
    }

    function onModalCancel() {
        if(props.node.attrs.isNew) {
            props.deleteNode();
            setTimeout(() => props.editor.commands.focus());
        }
    }

    function onModalInputChange() {
        const iframe = getIframe(html.value);
        invalid.value = !iframe;
        if(iframe) {
            iframe.removeAttribute('style');
            previewHtml.value = iframe.outerHTML;
        }
    }
    const debouncedOnModalInputChange = debounce(onModalInputChange, 200);
</script>

<template>
    <NodeRenderer class="editor__node d-inline-flex" :node="node">
        <template v-if="!node.attrs.isNew">
            <div class="card">
                <div class="card-body">
                    <iframe v-bind="node.attrs"></iframe>

                    <div class="mt-3">
                        <div class="row row-cols-auto gx-2">
                            <div>
                                <Button outline small @click="onEdit">
                                    {{ __('sharp::form.upload.edit_button') }}
                                </Button>
                            </div>
                            <div>
                                <Button variant="danger" outline small @click="onRemove">
                                    {{ __('sharp::form.upload.remove_button') }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <Modal
            v-model:visible="modalVisible"
            @ok="onModalOk"
            @close="onModalCancel"
            @cancel="onModalCancel"
            ref="modal"
        >
            <template v-slot:title>
                <template v-if="node.attrs.isNew">
                    {{ __('sharp::form.editor.dialogs.iframe.insert_title') }}
                </template>
                <template v-else>
                    {{ __('sharp::form.editor.dialogs.iframe.update_title') }}
                </template>
            </template>

            <textarea
                class="form-control"
                :class="{ 'border-red-600': invalid }"
                v-model="html"
                placeholder="&lt;iframe src=&quot;...&quot;&gt;&lt;/iframe&gt;"
                @input="debouncedOnModalInputChange"
                @paste="onModalInputChange"
                @focus="$event.target.select()"
                rows="6"
            ></textarea>

            <template v-if="previewHtml && !invalid">
                <div class="[&_iframe]:w-full [&_iframe]:max-h-[260px] [&_iframe[height$='%']]:h-[260px] mt-3" v-html="previewHtml">
                </div>
            </template>
        </Modal>
    </NodeRenderer>
</template>
