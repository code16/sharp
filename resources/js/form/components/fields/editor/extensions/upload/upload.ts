import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import UploadNode from "./UploadNode.vue";
import { getEventsPlugin } from "./events-plugin";
import { FormEditorFieldData, FormUploadFieldValueData } from "@/types";
import { FormFieldProps } from "@/form/components/types";

export type UploadOptions = {
    editorProps: FormFieldProps<FormEditorFieldData>,
    isReady: () => boolean,
    getFile: () => void,
    registerFile: () => Promise<FormUploadFieldValueData>,
    onRemove: (removedFile: FormUploadFieldValueData) => void,
    onUpdate: (updatedFile: FormUploadFieldValueData) => void,
}

export type UploadAttributes = {
    disk: string,
    path: string,
    name: string,
    size: number,
    thumbnail: string,
    filters: {
        crop: string,
        rotate: number,
    },
    file: File,
    isImage: boolean,
    uploaded: boolean,
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
                    const { uploaded, transformed, ...file } = attributes.file; // uploaded & transformed are only needed in editor "files" array
                    return {
                        file: JSON.stringify(file),
                    }
                },
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
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(UploadNode);
    },
});
