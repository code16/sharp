import { AnyCommands, Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import UploadNode from "./UploadNode.vue";
import { getEventsPlugin } from "./events-plugin";
import { FormUploadFieldValueData } from "@/types";
import { serializeUploadAttributeValue } from "@/embeds/utils/attributes";
import { UploadOptions } from "./index";

export type UploadAttributes = {
    file: FormUploadFieldValueData,
    htmlFile: File,
    isImage: boolean,
    notFound: boolean,
}

export const Upload = Node.create<UploadOptions>({
    name: 'upload',

    group: 'block',

    atom: true,

    isolating: true,

    priority: 150,

    addAttributes() {
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

    addCommands(): AnyCommands {
        return {
            insertUpload: ({ file, pos }) => ({ commands, tr }) => {
                return commands
                    .insertContentAt(pos ?? tr.selection.to, {
                        type: this.name,
                        attrs: {
                            nativeFile: file,
                            isImage: file.type.match(/^image\//),
                        },
                    });
            },
            newUpload: () => ({ editor }) => {
                /**
                 * @see UploadFileInput
                 */
                editor.emit('new-upload');
                return true;
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(UploadNode);
    },
});
