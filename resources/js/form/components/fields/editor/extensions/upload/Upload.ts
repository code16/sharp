import { Node, type Range, RawCommands } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import UploadNode from "./UploadNode.vue";
import { FormUploadFieldValueData } from "@/types";
import { serializeUploadAttributeValue } from "@/content/utils/attributes";
import { ExtensionAttributesSpec, WithRequiredOptions } from "@/form/components/fields/editor/types";
import { ContentUploadManager } from "@/content/ContentUploadManager";
import { Plugin } from "@tiptap/pm/state";
import { Form } from "@/form/Form";

export type UploadNodeAttributes = {
    file: FormUploadFieldValueData,
    legend: string,
    isNew: boolean,
    nativeFile: File,
    isImage: boolean,
    'data-unique-id': string,
    'data-thumbnail': string|null,
}

export type UploadOptions = {
    uploadManager: ContentUploadManager<Form>,
}

export const Upload: WithRequiredOptions<Node<UploadOptions>> = Node.create<UploadOptions>({
    name: 'upload',

    group: 'block',

    atom: true,

    isolating: true,

    priority: 150,

    addAttributes(): ExtensionAttributesSpec<UploadNodeAttributes> {
        return {
            file: {
                default: null,
                parseHTML: element => ({
                    file: JSON.parse(element.getAttribute('file') ?? 'null'),
                }),
                renderHTML: (attributes) => {
                    return {
                        file: serializeUploadAttributeValue(attributes.file),
                    }
                },
            },
            legend: {
                default: null,
            },
            'data-unique-id': {
                default: null,
            },
            'data-thumbnail': {
                default: null,
                rendered: false,
            },
            nativeFile: {
                default: null,
                rendered: false,
            },
            isNew: {
                default: false,
                rendered: false,
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
        if(node.attrs.isImage) {
            return ['x-sharp-image', HTMLAttributes];
        }
        return ['x-sharp-file', HTMLAttributes];
    },

    addCommands() {
        return {
            insertUpload: (file, pos) => ({ chain, tr }) => {
                return chain()
                    .withoutHistory()
                    .insertContentAt(pos ?? tr.selection.to, {
                        type: Upload.name,
                        attrs: {
                            file: null,
                            legend: null,
                            isNew: true,
                            nativeFile: file,
                            isImage: !!file?.type.match(/^image\//),
                            'data-unique-id': this.options.uploadManager.newId(),
                            'data-thumbnail': null,
                        } satisfies UploadNodeAttributes,
                    })
                    .run();
            },
        }
    },


    addProseMirrorPlugins() {
        return [
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
                            commands.insertUpload(file, this.editor.state.selection)
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
                                commands.insertUpload(file, coordinates.pos)
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
            insertUpload: (file?: File, pos?: number | Range) => ReturnType
        }
    }
}
