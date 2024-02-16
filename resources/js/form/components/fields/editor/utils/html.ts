/**
 * trim empty paragraphs at the end
 */
export function trimHTML(content, { inline }) {
    if(inline) {
        return content.replace(/<\/?p>/g, '');
    }
    return content.replace(/(<p>\s*<\/p>)+$/, '');
}
