import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import { DOMParser } from "prosemirror-model";
import HtmlNode from "./HtmlNode";

/**
 * @returns HTMLElement
 */
function elementFromString(value) {
    const wrappedValue = `<body>${value}</body>`
    return new window.DOMParser().parseFromString(wrappedValue, 'text/html').body
}

function updateContent(content, schema) {
    const dom = elementFromString(content);
    const parser = DOMParser.fromSchema(schema);
    [...dom.children].forEach(node => {
        const slice = parser.parseSlice(node).content;
        if(!slice.size) {
            const div = document.createElement('div');
            div.setAttribute('data-html-content', '');
            node.parentElement.replaceChild(div, node);
            div.appendChild(node);
        }
    });
    return dom.innerHTML;
}

export const Html = Node.create({
    name: 'html-content',
    group: 'block',
    onBeforeCreate() {
        this.editor.options.content = updateContent(this.editor.options.content, this.editor.schema);
    },
    addAttributes() {
        return {
            content: {
                default: '',
                parseHTML(element) {
                    return element.getAttribute('data-html-content') || element.innerHTML;
                },
            },
        }
    },
    renderHTML({ node }) {
        const parent = document.createElement('div');
        const content = node.attrs.content.trim();
        parent.innerHTML = content;
        if(parent.innerHTML === content) {
            parent.setAttribute('data-html-content', '');
        } else {
            parent.innerHTML = '';
            parent.setAttribute('data-html-content', node.attrs.content);
        }
        return parent;
    },
    parseHTML() {
        return [
            {
                tag: '[data-html-content]',
            }
        ]
    },
    addNodeView() {
        return VueNodeViewRenderer(HtmlNode);
    },
});
