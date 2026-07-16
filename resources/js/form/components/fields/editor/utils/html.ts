/**
 * trim empty paragraphs at the end
 */
export function trimHTML(content: string, { inline }: { inline: boolean }) {
    if(inline) {
        return content.replace(/<\/?p>/g, '');
    }
    return content
        .replace(/(?:<p>\s*<\/p>)+(<ol class="footnotes">.+?<\/ol>)?$/, '$1');
}
