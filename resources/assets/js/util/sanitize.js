import DOMPurify from 'dompurify';

/**
 * Sanitize HTML content to prevent XSS attacks
 * @param {string|null} html - The HTML content to sanitize
 * @returns {string|null} - The sanitized HTML content
 */
export function sanitize(html) {
    if (!html) {
        return html;
    }
    return DOMPurify.sanitize(html, {
        CUSTOM_ELEMENT_HANDLING: {
            tagNameCheck: () => true,
            attributeNameCheck: () => true,
        }
    });
}
