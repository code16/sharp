import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import MarkdownUpload from "./MarkdownUpload";
import FileInput from "./UploadFileInput";
import { serializeFilterCrop, serializeFilterRotate } from "./util";

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
            value: {
                default: null,
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: 'x-sharp-media',
                getAttrs: node => ({
                    value: this.options.findFile({
                        disk: node.getAttribute('disk'),
                        path: node.getAttribute('path'),
                        name: node.getAttribute('name'),
                    }),
                }),
            },
        ]
    },

    /**
     * <x-sharp-media src="example.jpg">
     * </x-sharp-media>
     */
    renderHTML({ HTMLAttributes }) {
        const value = HTMLAttributes.value;
        return [
            'x-sharp-media',
            {
                'name': value?.name,
                'disk': value?.disk,
                'path': value?.path,
                'filter-crop': serializeFilterCrop(value),
                'filter-rotate': serializeFilterRotate(value),
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
                 * @see FileInput
                 */
                editor.emit('new-upload');
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(MarkdownUpload);
    },
});
