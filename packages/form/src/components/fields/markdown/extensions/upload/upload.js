import { Node, mergeAttributes } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import MarkdownUpload from "./MarkdownUpload";

export const Upload = Node.create({
    name: 'upload',

    group: 'block',

    defaultOptions: {
        HTMLAttributes: {},
        fieldOptions: {},
    },

    addAttributes() {
        return {
            src: {
                default: null,
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: 'img[src]',
                getAttrs: node => node.src.match(/^local:/) || null,
            },
            {
                tag: 'x-sharp-upload',
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['x-sharp-upload', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes)];
    },

    addCommands() {
        return {
            setUpload: options => ({ commands }) => {
                return commands.insertContent({
                    type: this.name,
                    attrs: options,
                })
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(MarkdownUpload);
    },
});
