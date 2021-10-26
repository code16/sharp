
/**
 * normalize HTML coming from legacy Trix output
 */
export function normalizeHTML(html) {
    const dom = document.createElement('div');
    dom.innerHTML = html;

    normalizeAdjacentDivs(dom);
    normalizeParagraphs(dom);

    return dom.innerHTML;
}

function normalizeAdjacentDivs(dom) {
    elementDivs(dom)
        .forEach(div => {
            if(div.previousElementSibling?.matches?.('div')) {
                const previousDiv = div.previousElementSibling;
                const lastChild = previousDiv.childNodes[previousDiv.childNodes.length - 1];
                if(!lastChild?.matches?.('br')) {
                    div.innerHTML = `${previousDiv.innerHTML}<br>${div.innerHTML}`;
                    previousDiv.remove();
                }
            }
        });
}

function normalizeParagraphs(dom) {
    elementDivs(dom)
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
}

function elementDivs(el) {
    return [...el.children]
        .filter(el => el.matches('div') && !el.attributes.length)
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
