import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import UploadNode from "./UploadNode.vue";
import { getEventsPlugin } from "./events-plugin";

export const Upload = Node.create({
    name: 'upload',

    group: 'block',

    atom: true,

    isolating: true,

    priority: 150,

    addOptions: () => ({
        fieldProps: {},
        isReady: () => true,
        getFile: () => {},
        registerFile: () => {},
        onInput: () => {},
        onRemove: () => {},
        onUpdate: () => {},
    }),

    addAttributes() {
        return {
            file: {
                default: null,
                parseHTML: element => ({
                    file: JSON.parse(element.getAttribute('file') ?? 'null'),
                }),
                renderHTML: (attributes) => JSON.stringify(attributes.file),
            },
            /**
             * @type File
             */
            nativeFile: {
                default: null,
                renderHTML: () => null,
            },
            isImage: {
                default: false,
                parseHTML: element => element.matches('x-sharp-image'),
                renderHTML: () => null,
            },
            notFound: {
                default: false,
                renderHTML: () => null,
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

    addProseMirrorPlugins() {
        return [
            getEventsPlugin(this.editor),
        ]
    },

    addCommands() {
        return {
            insertUpload: ({ file, pos }) => ({ commands, tr }) => {
                return commands
                    .insertContentAt(pos ?? tr.selection.to, {
                        type: this.name,
                        attrs: {
                            file,
                            isImage: file.type.match(/^image\//),
                        },
                    });
            },
            newUpload: () => ({ editor }) => {
                /**
                 * @see UploadFileInput
                 */
                editor.emit('new-upload');
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(UploadNode);
    },
});
