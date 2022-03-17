import { DOMParser } from "prosemirror-model";
import { elementFromString } from "../../util/dom";

function prosemirrorParse(node, schema) {
    const parser = DOMParser.fromSchema(schema);
    const dom = elementFromString(node.outerHTML);
    return parser.parseSlice(dom).content;
}

export function setupContent(content, schema) {
    const dom = elementFromString(content);
    return setupContentDOM(dom, schema);
}

export function setupContentDOM(dom, schema) {
    [...dom.children].forEach(node => {
        const parsed = prosemirrorParse(node, schema);
        if(!parsed.size) {
            const div = document.createElement('div');
            div.setAttribute('data-html-content', 'true');
            node.parentElement.replaceChild(div, node);
            div.appendChild(node);
        }
    });
    return dom.innerHTML;
}
