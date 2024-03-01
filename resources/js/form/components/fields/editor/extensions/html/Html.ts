import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import HtmlNode from "./HtmlNode.vue";
import { setupContent, setupContentDOM } from "./utils";
import { ExtensionAttributesSpec } from "@/form/components/fields/editor/types";

export type HtmlContentNodeAttributes = {
    content: string,
    isNew: boolean
}

export const Html = Node.create({
    name: 'html-content',
    group: 'block',

    addAttributes(): ExtensionAttributesSpec<HtmlContentNodeAttributes> {
        return {
            content: {
                default: '',
                parseHTML(element) {
                    return element.innerHTML.trim();
                },
            },
            isNew: {
                default: false,
                rendered: false,
            },
        }
    },

    addStorage() {
        return {
            markdown: {
                parse: {
                    updateDOM(dom) {
                        setupContentDOM(dom, this.editor.schema);
                    },
                }
            }
        }
    },

    onBeforeCreate() {
        if(!this.editor.storage.markdown) {
            this.editor.setOptions({
                content: setupContent(
                    this.editor.options.content,
                    this.editor.schema
                )
            });
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
                        isNew: true,
                    },
                })
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(HtmlNode);
    },
});

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        'html-content': {
            insertHtml: () => ReturnType
        }
    }
}
