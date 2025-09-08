import { Node, type Range } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import UploadNode from "./UploadNode.vue";
import { ExtensionAttributesSpec, WithRequiredOptions } from "@/form/components/fields/editor/types";
import { ContentUploadManager } from "@/content/ContentUploadManager";
import { Plugin, Transaction } from "@tiptap/pm/state";
import { Form } from "@/form/Form";
import { FormEditorUploadData } from "@/content/types";
import { Fragment, Slice } from "@tiptap/pm/model";
import { getAllNodesAfterUpdate } from "@/form/components/fields/editor/utils/tiptap/getAllNodesAfterUpdate";
import { FormUploadFieldValueData } from "@/types";


export type UploadNodeAttributes = {
    'data-key': string,
    'data-value'?: FormEditorUploadData,
    isImage: boolean,
}

export type UploadOptions = {
    uploadManager: ContentUploadManager<Form>,
    locale: string | null
}

export const Upload: WithRequiredOptions<Node<UploadOptions>> = Node.create<UploadOptions>({
    name: 'upload',

    group: 'embed',

    atom: true,

    isolating: true,

    draggable: true,

    priority: 150,

    addAttributes(): ExtensionAttributesSpec<UploadNodeAttributes> {
        return {
            'data-key': {},
            'data-value': {
                parseHTML(element) {
                    return element.hasAttribute('data-value')
                        ? JSON.parse(element.getAttribute('data-value'))
                        : null;
                },
                renderHTML(attributes: UploadNodeAttributes) {
                    return {
                        'data-value': attributes['data-value']
                            ? JSON.stringify(attributes['data-value'])
                            : null
                    };
                },
            },
            isImage: {
                default: false,
                parseHTML: element => element.matches('x-sharp-image'),
                rendered: false,
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: 'x-sharp-image',
            },
            {
                tag: 'x-sharp-file',
            },
        ]
    },

    renderHTML({ node, HTMLAttributes }) {
        if((node.attrs as UploadNodeAttributes).isImage) {
            return ['x-sharp-image', HTMLAttributes];
        }
        return ['x-sharp-file', HTMLAttributes];
    },

    addCommands() {
        return {
            insertUpload: ({ id, nativeFile, type, pos }) => ({ commands, tr }) => {
                id ??= this.options.uploadManager.newUpload(this.options.locale, {
                    file: { nativeFile } as FormUploadFieldValueData, // auto upload with this file
                });

                return commands.insertContentAt(pos ?? tr.selection.to, {
                    type: Upload.name,
                    attrs: {
                        'data-key': id,
                        isImage: !!(type ?? nativeFile.type).match(/^image\//),
                    } satisfies UploadNodeAttributes,
                });
            },
        }
    },

    onUpdate({ transaction, appendedTransactions }) {
        this.options.uploadManager.syncUploads(
            this.options.locale,
            getAllNodesAfterUpdate(this.name, transaction, appendedTransactions)
                .map(node => node.attrs['data-key']),
        )
    },


    addProseMirrorPlugins() {
        const { name, options } = this;

        return [
            new Plugin({
                props: {
                    transformCopied(slice: Slice) {
                        if(!slice.content.content.find(n => n.type.name === name)) {
                            return slice;
                        }
                        return new Slice(
                            Fragment.fromArray(
                                slice.content.content.map(node => {
                                    if(node.type.name === name) {
                                        return node.type.create({
                                            ...node.attrs,
                                            'data-key': null,
                                            'data-value': options.uploadManager.getUpload(node.attrs['data-key']),
                                        })
                                    }
                                    return node;
                                })
                            ),
                            slice.openStart,
                            slice.openEnd
                        );
                    },
                    transformPasted(slice: Slice) {
                        if(!slice.content.content.find(n => n.type.name === name)) {
                            return slice;
                        }
                        return new Slice(
                            Fragment.fromArray(
                                slice.content.content.map(node => {
                                    if(node.type.name === name && node.attrs['data-key'] == null) {
                                        return node.type.create({
                                            'data-key': options.uploadManager.newUpload(options.locale, node.attrs['data-value']),
                                            isImage: node.attrs.isImage,
                                        });
                                    }
                                    return node;
                                })
                            ),
                            slice.openStart,
                            slice.openEnd
                        )
                    },
                }
            }),
            new Plugin({
                props: {
                    handlePaste: (view, event) => {
                        const clipboardData = event.clipboardData;

                        if(!clipboardData.files.length) {
                            return;
                        }

                        event.preventDefault();

                        const commands = this.editor.chain();

                        [...clipboardData.files].forEach(file => {
                            commands.insertUpload({ nativeFile: file, pos: this.editor.state.selection })
                        });

                        commands.run();

                        return false;
                    },
                    handleDOMEvents: {
                        drop: (view, event) => {
                            if (!event.dataTransfer?.files?.length) {
                                return
                            }

                            event.preventDefault();

                            const coordinates = view.posAtCoords({ left: event.clientX, top: event.clientY });

                            const commands = this.editor.chain();

                            [...event.dataTransfer.files].forEach(file => {
                                commands.insertUpload({ nativeFile: file, pos: coordinates.pos })
                            });

                            commands.run();

                            return true;
                        },
                    },
                },
            }),
        ]
    },

    addNodeView() {
        return VueNodeViewRenderer(UploadNode);
    },
});

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        upload: {
            insertUpload: (params: { id?: string, nativeFile?: File, type?: string, pos?: number | Range }) => ReturnType
        }
    }
}
