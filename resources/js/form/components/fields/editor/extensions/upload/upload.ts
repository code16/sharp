import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import UploadNode from "./UploadNode.vue";
import {
    parseFilterCrop,
    serializeFilterCrop,
    parseFilterRotate,
    serializeFilterRotate,
} from "@/utils/upload";
import { getEventsPlugin } from "./events-plugin";
import { FormEditorFieldData, FormUploadFieldValueData } from "@/types";
import { FormFieldProps } from "@/form/components/types";

export type UploadOptions = {
    fieldProps: FormFieldProps<FormEditorFieldData>,
    isReady: () => boolean,
    getFile: () => void,
    registerFile: () => Promise<FormUploadFieldValueData>,
    onInput: () => void,
    onRemove: () => void,
    onUpdate: () => void,
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
            disk: {
                default: null,
            },
            path: {
                default: null,
            },
            name: {
                default: null,
            },
            size: {
                default: null,
                renderHTML: () => null
            },
            thumbnail: {
                default: null,
                renderHTML: () => null
            },
            filters: {
                parseHTML: element => ({
                    crop: parseFilterCrop(element.getAttribute('filter-crop')),
                    rotate: parseFilterRotate(element.getAttribute('filter-rotate')),
                }),
                renderHTML: () => null,
            },
            'filter-crop': {
                default: null,
                renderHTML: attributes => ({
                    'filter-crop': serializeFilterCrop(attributes.filters?.crop),
                }),
            },
            'filter-rotate': {
                default: null,
                renderHTML: attributes => ({
                    'filter-rotate': serializeFilterRotate(attributes.filters?.rotate),
                }),
            },
            /**
             * @type File
             */
            file: {
                default: null,
                renderHTML: () => null,
            },
            isImage: {
                default: false,
                parseHTML: element => element.matches('x-sharp-image'),
                renderHTML: () => null,
            },
            uploaded: {
                default: false,
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
