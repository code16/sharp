import {ignoreVueElement} from "@/util/vue";


ignoreVueElement('tw-scoped');

customElements.define('tw-scoped', class extends HTMLElement {
    constructor() {
        super();

        // Create a shadow root for the custom element
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        // Import the CSS file and attach it to the shadow root
        const linkElem = document.createElement('link');
        linkElem.setAttribute('rel', 'stylesheet');
        linkElem.setAttribute('href', document.querySelector('link[href*="/resources/css/app"]').getAttribute('href'));

        this.shadowRoot.appendChild(linkElem);

        // Render the slot content inside the shadow root
        this.shadowRoot.innerHTML = `
          <slot></slot>
        `;
    }
});
