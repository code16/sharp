import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import HtmlNode from "./HtmlNode";
import { setupContent } from "./util";

export const Html = Node.create({
    name: 'html-content',
    group: 'block',
    onBeforeCreate() {
        this.editor.options.content = setupContent(
            this.editor.options.content,
            this.editor.schema
        );
    },
    addAttributes() {
        return {
            content: {
                default: '',
                parseHTML(element) {
                    return element.innerHTML.trim();
                },
            },
            new: {
                default: false,
            },
        }
    },
    renderHTML({ node }) {
        const parent = document.createElement('div');
        parent.setAttribute('data-html-content', 'true');
        parent.innerHTML = node.attrs.content;
        return parent;
    },
    parseHTML() {
        return [
            {
                tag: '[data-html-content]',
            }
        ]
    },
    addCommands() {
        return {
            insertHtml: () => ({ commands }) => {
                return commands.insertContent({
                    type: this.name,
                    attrs: {
                        new: true,
                    },
                })
            },
        }
    },
    addNodeView() {
        return VueNodeViewRenderer(HtmlNode);
    },
});
