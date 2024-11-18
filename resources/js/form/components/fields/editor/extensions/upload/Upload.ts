import { Node, type Range } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import UploadNode from "./UploadNode.vue";
import { ExtensionAttributesSpec, WithRequiredOptions } from "@/form/components/fields/editor/types";
import { ContentUploadManager } from "@/content/ContentUploadManager";
import { Plugin, Transaction } from "@tiptap/pm/state";
import { ReplaceStep } from "@tiptap/pm/transform";
import { Form } from "@/form/Form";



export type UploadNodeAttributes = {
    'data-key': string,
    isImage: boolean,
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
            'data-key': {},
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
                id ??= this.options.uploadManager.newUpload(nativeFile);

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

    onTransaction({ transaction }) {
        function getAddedAndRemovedNodes(transaction: Transaction, nodeName, schema) {
            const nodeType = schema.nodes[nodeName];
            if (!nodeType) {
                console.warn(`Node type ${nodeName} does not exist in the schema.`);
                return { addedNodes: [], removedNodes: [] };
            }

            const addedNodes = [];
            const removedNodes = [];

            for (let step of transaction.steps) {
                const stepMap = step.getMap();

                // Check for removed nodes
                stepMap.forEach((oldStart, oldEnd) => {
                    transaction.before.content.nodesBetween(oldStart, oldEnd, (node, pos) => {
                        if (node.type === nodeType) {
                            removedNodes.push({ node, pos });
                        }
                    });
                });

                // Check for added nodes
                if (step.slice && step.slice.content) {
                    let addedPos = stepMap.map(step.from);
                    step.slice.content.descendants((node, pos) => {
                        if (node.type === nodeType) {
                            addedNodes.push({ node, pos: addedPos + pos });
                        }
                        return true;
                    });
                }
            }

            return { addedNodes, removedNodes };
        }

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
