import DOMPurify from 'dompurify';

DOMPurify.addHook('afterSanitizeAttributes', function (node) {
    if (node.tagName === 'A' && !node.getAttribute('rel')?.includes('noopener')) {
        node.setAttribute('rel', `${node.getAttribute('rel') ?? ''} noopener`.trim());
    }
});

export function sanitize(html: string | null) {
    return html
        ? DOMPurify.sanitize(html, {
            ADD_TAGS: ['iframe'],
            ADD_ATTR: ['target'],
            CUSTOM_ELEMENT_HANDLING: {
                tagNameCheck: () => true,
                attributeNameCheck: (name) => {
                    return !name.match(/^(v-)|:|@|#/); // remove vue related attributes
                },
            },
        })
        : html;
}

// Separate function that sanitizes all rendered Vue template, '{{' & '}}' should always be escaped
export function sanitizeVueTemplate(template: string) {
    return template.replaceAll('{{', '&lcub;&lcub;').replaceAll('}}', '&rcub;&rcub;');
}
