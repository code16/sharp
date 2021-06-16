import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import MarkdownUpload from "./MarkdownUpload";
import FileInput from "./UploadFileInput";

export const Upload = Node.create({
    name: 'upload',

    group: 'block',

    isolating: true,

    priority: 150,

    defaultOptions: {
        fieldOptions: {},
        onInput: () => {},
        onRemove: () => {},
        onUpdate: () => {},
        getFileByName: () => {},
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
                tag: 'img[src^="local:"]',
                getAttrs: node => {
                    return {
                        value: this.options.getFileByName(node.src),
                    }
                }
            },
            {
                tag: 'x-sharp-upload',
                getAttrs: node => ({
                    value: this.options.getFileByName(node.getAttribute('src')),
                }),
            },
        ]
    },

    /**
     * <x-sharp-upload src="example.jpg">
     * </x-sharp-upload>
     */
    renderHTML({ HTMLAttributes }) {
        const value = HTMLAttributes.value;
        return [
            'x-sharp-upload',
            {
                'src': value?.name,
                'crop-data': value?.cropData,
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
