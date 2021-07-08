
/**
 * normalize HTML coming from legacy Trix output
 */
export function normalizeHTML(html) {
    const dom = document.createElement('div');
    dom.innerHTML = html;

    [...dom.children]
        .filter(el => el.matches('div') && !el.attributes.length)
        .forEach(div => {
            trimNewLines(div);
            if(div.childNodes.length) {
                const p = document.createElement('p');
                p.innerHTML = div.innerHTML;
                dom.replaceChild(p, div);
            } else {
                div.remove();
            }
        });

    return dom.innerHTML;
}

function trimNewLines(div) {
    [...div.childNodes]
        .reverse()
        .slice(0, 2)
        .filter(node => node.matches?.('br'))
        .forEach(node => {
            if(!node.nextSibling) {
                node.remove()
            }
        });
}
