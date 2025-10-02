import DOMPurify from 'dompurify';

export function sanitize(html: string | null) {
    return html
        ? DOMPurify.sanitize(html, {
            ADD_TAGS: ['iframe'],
            CUSTOM_ELEMENT_HANDLING: {
                tagNameCheck: () => true,
                attributeNameCheck: () => true,
            },
        })
        : html;
}
