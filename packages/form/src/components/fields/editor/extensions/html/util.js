import { DOMParser } from "prosemirror-model";

/**
 * @returns HTMLElement
 */
export function elementFromString(value) {
    const wrappedValue = `<body>${value}</body>`
    return new window.DOMParser().parseFromString(wrappedValue, 'text/html').body
}


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
        // in some cases we want to extract the html content if known (e.g. embeds)
        if(node.hasAttribute('data-html-content') && node.children.length === 1) {
            const childFragment = prosemirrorParse(node.firstElementChild, schema);
            if(childFragment.firstChild?.type.name.startsWith('embed:')) {
                node.parentElement.replaceChild(node.firstElementChild, node);
            }
            return;
        }

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
