import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import HtmlNode from "./HtmlNode";
import { setupContent, setupContentDOM } from "./util";
import { createMarkdownExtension } from "tiptap-markdown";

export const Html = Node.create({
    name: 'html-content',
    group: 'block',
    onBeforeCreate() {
        if(this.editor.options.markdown) {
            const { extensions } = this.editor.options.markdown;
            this.editor.setOptions({
                markdown: {
                    extensions: [
                        ...(extensions ?? []),
                        createMarkdownExtension(this, {
                            parse: {
                                updateDOM(dom) {
                                    setupContentDOM(dom, this.schema);
                                },
                            }
                        })
                    ],
                }
            });
        } else {
            this.editor.setOptions({
                content: setupContent(
                    this.editor.options.content,
                    this.editor.schema
                )
            });
        }
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
