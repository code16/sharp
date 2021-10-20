import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";

export const Html = Node.create({
    name: 'html-content',
    group: 'block',
    atom: true,
    isolating: true,
    addAttributes() {
        return {
            content: {
                default: null,
                parseHTML(element) {
                    return element.getAttribute('data-html-content');
                },
            },
        }
    },
    renderHTML({ node }) {
        const parent = document.createElement('div');
        parent.innerHTML = node.attrs.content;
        if(parent.innerHTML !== node.attrs.content) {
            parent.innerHTML = '';
        }
        parent.setAttribute('data-html-content', node.attrs.content);
        return parent;
    },
    addNodeView() {
        return VueNodeViewRenderer({
            template: `<div data-node-view-wrapper="">
                <input class="w-100" :value="node.attrs.content" @input="handleInput" />
            </div>`,
            props: {
                node: Object,
                updateAttributes: Function,
                editor: Object,
            },
            methods: {
                handleInput(e) {
                    this.updateAttributes({
                        content: e.target.value,
                    });
                    console.log(JSON.stringify(this.editor.getMarkdown()));
                },
            },
        });
    },
    parseHTML() {
        return [
            {
                tag: '[data-html-content]',
            }
        ]
    }
});
