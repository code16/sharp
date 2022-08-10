const Prism = require('prismjs');
const loadLanguages = require('prismjs/components/');

loadLanguages(['php']);

module.exports = {
    name: 'markdown',
    extendsMarkdown: md => {
        md.renderer.rules['code_inline'] = (tokens, idx, options, env, slf) => {
            const token = tokens[idx];
            const highlighted = Prism.highlight(token.content, Prism.languages.php);
            const inlineNodes = tokens.filter(token =>
                token.type === 'text' && token.content.trim()
                || token.type === 'code_inline'
            );
            const isFullwidth = inlineNodes.length === 1;
            return `<code class="inline ${isFullwidth ? 'full' : ''}" v-pre>${highlighted}</code>`;
        };
    }
}
