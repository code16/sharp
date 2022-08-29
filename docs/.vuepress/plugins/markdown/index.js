import Prism from 'prismjs';
import loadLanguages from 'prismjs/components/index.js';

loadLanguages(['php']);

export default {
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
