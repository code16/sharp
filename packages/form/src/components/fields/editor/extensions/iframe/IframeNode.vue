<template>
    <NodeRenderer class="editor__node d-inline-flex" :node="node">
        <template v-if="!node.attrs.isNew">
            <div class="card">
                <div class="card-body">
                    <iframe v-bind="node.attrs"></iframe>

                    <div class="mt-3">
                        <div class="row row-cols-auto gx-2">
                            <div>
                                <Button outline small @click="handleEditClicked">
                                    {{ lang('form.upload.edit_button') }}
                                </Button>
                            </div>
                            <div>
                                <Button variant="danger" outline small @click="handleRemoveClicked">
                                    {{ lang('form.upload.remove_button') }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <Modal
            :visible.sync="modalVisible"
            @ok="handleModalOk"
            @close="handleModalCancel"
            @cancel="handleModalCancel"
            @shown="handleModalShown"
            ref="modal"
        >
            <template v-slot:title>
                <template v-if="node.attrs.isNew">
                    {{ lang('form.editor.dialogs.iframe.insert_title') }}
                </template>
                <template v-else>
                    {{ lang('form.editor.dialogs.iframe.update_title') }}
                </template>
            </template>

            <textarea
                class="form-control"
                :class="{ 'is-invalid': invalid }"
                v-model="html"
                placeholder="&lt;iframe src=&quot;...&quot;&gt;&lt;/iframe&gt;"
                @input="handleInput"
                @paste="handleChanged"
                @focus="$event.target.select()"
                rows="6"
                ref="textarea"
            ></textarea>

            <template v-if="previewHtml && !invalid">
                <div class="iframe-node__modal-renderer mt-3" v-html="previewHtml">
                </div>
            </template>
        </Modal>
    </NodeRenderer>
</template>

<script>
    import debounce from 'lodash/debounce';
    import { lang } from "sharp";
    import { Modal, Button } from "sharp-ui";
    import NodeRenderer from "../../NodeRenderer.vue";
    import { getHTMLFromFragment } from "@tiptap/core";
    import { Fragment } from "@tiptap/pm/model";

    export default {
        components: {
            Modal,
            NodeRenderer,
            Button,
        },
        props: {
            editor: Object,
            node: Object,
            selected: Object,
            extension: Object,
            getPos: Function,
            updateAttributes: Function,
            deleteNode: Function,
        },
        data() {
           return {
               html: null,
               previewHtml: null,
               invalid: false,
               modalVisible: this.node.attrs.isNew,
           }
        },
        methods: {
            lang,
            getIframe(html) {
                const dom = document.createElement('div');
                dom.innerHTML = html;
                return dom.querySelector('iframe');
            },
            handleRemoveClicked() {
                this.deleteNode();
            },
            handleEditClicked() {
                const rendered = getHTMLFromFragment(Fragment.from(this.node), this.editor.schema);
                this.html = this.getIframe(rendered).outerHTML;
                this.previewHtml = this.html;
                this.modalVisible = true;
                this.invalid = false;
            },
            handleChanged() {
                const iframe = this.getIframe(this.html);
                this.invalid = !iframe;
                if(iframe) {
                    iframe.removeAttribute('style');
                    this.previewHtml = iframe.outerHTML;
                }
            },
            handleInput() {
                this.handleChanged();
            },
            handleModalOk(e) {
                const iframe = this.getIframe(this.html);
                if(iframe) {
                    this.updateAttributes({
                        ...this.node.type.defaultAttrs,
                        ...Object.fromEntries(
                            [...iframe.attributes].map(attr => [attr.name, attr.value])
                        ),
                        isNew: false,
                    });
                    this.modalVisible = false;
                } else {
                    e.preventDefault();
                }
            },
            handleModalCancel() {
                if(this.node.attrs.isNew) {
                    this.deleteNode();
                    setTimeout(() => this.editor.commands.focus());
                }
            },
            handleModalShown() {
                if(this.node.attrs.isNew) {
                    this.$refs.textarea.focus();
                }
            },
        },
        created() {
            this.handleInput = debounce(this.handleInput, 200);
        },
    }
</script>
