import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import UploadNode from "./UploadNode";
import {
    parseFilterCrop,
    serializeFilterCrop,
    parseFilterRotate,
    serializeFilterRotate,
} from "sharp-files";


export const Upload = Node.create({
    name: 'upload',

    group: 'block',

    atom: true,

    isolating: true,

    priority: 150,

    defaultOptions: {
        fieldOptions: {},
        onInput: () => {},
        onRemove: () => {},
        onUpdate: () => {},
        findFile: ({ disk, path, name }) => {},
    },

    addAttributes() {
        return {
            'disk': {
                default: null,
            },
            'path': {
                default: null,
            },
            'name': {
                default: null,
            },
            'filter-crop': {
                default: null,
            },
            'filter-rotate': {
                default: null,
            },
            /**
             * @type File
             */
            'file': {
                default: null,
            }
        }
    },

    parseHTML() {
        return [
            {
                tag: 'x-sharp-media',
                getAttrs: node => ({
                    'disk': node.getAttribute('disk'),
                    'path': node.getAttribute('path'),
                    'name': node.getAttribute('name'),
                    'filter-crop': parseFilterCrop(node.getAttribute('filter-crop')),
                    'filter-rotate': parseFilterRotate(node.getAttribute('filter-rotate')),
                }),
            },
        ]
    },

    /**
     * <x-sharp-media path="example.jpg">
     * </x-sharp-media>
     */
    renderHTML({ HTMLAttributes }) {
        return [
            'x-sharp-media',
            {
                'name': HTMLAttributes['name'],
                'disk': HTMLAttributes['disk'],
                'path': HTMLAttributes['path'],
                'filter-crop': serializeFilterCrop(HTMLAttributes['filter-crop']),
                'filter-rotate': serializeFilterRotate(HTMLAttributes['filter-rotate']),
            }
        ];
    },

    addCommands() {
        return {
            insertUpload: attrs => ({ commands }) => {
                return commands.insertContent({
                    type: this.name,
                    attrs,
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
