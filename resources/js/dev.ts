
function getStyleSheetFromUrl(cssUrl) {
    const styleSheet = new CSSStyleSheet();
    return fetch(cssUrl, { headers: { 'Accept': 'text/css' } })
        .then((response) => response.text())
        .then((cssText) => {
            styleSheet.replaceSync(cssText);
            return styleSheet;
        });
}

const sheetPromises = [
    getStyleSheetFromUrl(document.querySelector('meta[name="tw-style"]').getAttribute('content')),
    getStyleSheetFromUrl(document.querySelector('link[href*="/resources/sass/vendor"]').getAttribute('href')),
];

customElements.define('tw-scoped', class extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        sheetPromises.forEach((sheetPromise) => {
            sheetPromise.then((styleSheet) => {
                this.shadowRoot.adoptedStyleSheets.push(styleSheet);
            });
        });
    }

    connectedCallback() {
        this.shadowRoot.append(...this.childNodes);
    }
});
