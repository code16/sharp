import { JSDOM } from "jsdom";
import Prism from 'prismjs';
import loadLanguages from 'prismjs/components/index.js';

loadLanguages(['php']);

export function transformContent(html: string) {
    const content = new JSDOM(html).window.document.querySelector('h1')?.parentElement;

    if(!content) {
        return html;
    }

    // prevent breaking on first dash
    [...content.querySelectorAll(':not(pre) > code')]
        .forEach(code => {
            code.innerHTML = code.innerHTML.replace(/-&gt;/g, '-&NoBreak;&gt;');
            console.log(code.innerHTML);
        });

    // apply syntax highlight to h3 with sole code element
    [...content.querySelectorAll(':is(h3, h4) > code')]
        .filter(code =>
            ![...code.parentElement.childNodes].some(node => node.nodeName === '#text' && node.textContent.trim())
        )
        .forEach(code => {
            code.classList.add('inline', 'full');
            code.innerHTML = Prism.highlight(code.innerHTML,  Prism.languages.php, 'php');
            code.querySelectorAll('.token').forEach(token => {
                if(!token.matches('.function')) {
                    token.setAttribute('data-content', token.innerHTML);
                    token.innerHTML = '';
                }
            });
        });

    // add margin-left to all following elements
    // let isCodeDescription = false;
    // [...content.children].forEach(element => {
    //     if(isCodeDescription && (isCodeDescription = element.matches(':not(h2, h3)'))) {
    //         element.classList.add('code-description');
    //     } else {
    //         isCodeDescription = element.matches('h3') && !!element.querySelector('code.full');
    //     }
    // });

    return content.innerHTML;
}
