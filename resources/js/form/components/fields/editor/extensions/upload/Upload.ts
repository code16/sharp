import { Node, type Range, RawCommands } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import UploadNode from "./UploadNode.vue";
import { ExtensionAttributesSpec, WithRequiredOptions } from "@/form/components/fields/editor/types";
import { ContentUploadManager } from "@/content/ContentUploadManager";
import { Plugin } from "@tiptap/pm/state";
import { Form } from "@/form/Form";
import UploadModal from "@/form/components/fields/editor/extensions/upload/UploadModal.vue";
import { Ref } from "vue";



export type UploadNodeAttributes = {
    'data-key': string,
    // isNew: boolean,
    isImage: boolean,
    droppedFile: File,
}

export type UploadOptions = {
    uploadManager: ContentUploadManager<Form>,
    uploadModal: Ref<InstanceType<typeof UploadModal>>,
}

export const Upload: WithRequiredOptions<Node<UploadOptions>> = Node.create<UploadOptions>({
    name: 'upload',

    group: 'block',

    atom: true,

    isolating: true,

    priority: 150,

    addAttributes(): ExtensionAttributesSpec<UploadNodeAttributes> {
        return {
            'data-key': {},
            droppedFile: {
                default: null,
                rendered: false,
            },
            // isNew: {
            //     default: false,
            //     rendered: false,
            // },
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
                const commands = chain();
                //
                // if(!file) {
                //     // we want the user to select file / validate modal before writing to editor history (undo/redo)
                //     commands.withoutHistory();
                // }

                const id = this.options.uploadManager.insertUpload();

                return commands.insertContentAt(pos ?? tr.selection.to, {
                        type: Upload.name,
                        attrs: {
                            'data-key': id,
                            droppedFile: file,
                            isImage: !!file?.type.match(/^image\//),
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
