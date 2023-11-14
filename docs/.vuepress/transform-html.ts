import { JSDOM } from "jsdom";
import Prism from 'prismjs';
import loadLanguages from 'prismjs/components/index.js';

loadLanguages(['php']);

export function transformHtml(html) {
    const content = new JSDOM(html).window.document.body;

    // apply syntax highlight to h3 with sole code element
    [...content.querySelectorAll('h3 > code')]
        .filter(code =>
            ![...code.parentElement.childNodes].some(node => node.nodeName === '#text' && node.textContent.trim())
        )
        .forEach(code => {
            code.classList.add('inline', 'full');
            code.innerHTML = Prism.highlight(code.innerHTML,  Prism.languages.php, 'php');
        })

    // add margin-left to all following elements
    let isCodeDescription = false;
    [...content.children].forEach(/** @param {HTMLElement} element */element => {
        if(isCodeDescription && (isCodeDescription = element.matches(':not(h2, h3)'))) {
            element.classList.add('code-description');
        } else {
            isCodeDescription = element.matches('h3') && element.querySelector('code.full');
        }
    });

    return content.innerHTML;
}
