import DOMPurify from 'dompurify';

export function sanitize(html: string | null) {
    return html
        ? DOMPurify.sanitize(html, {
            ADD_TAGS: ['iframe'],
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
