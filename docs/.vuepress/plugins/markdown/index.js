import Prism from 'prismjs';
import loadLanguages from 'prismjs/components/index.js';

loadLanguages(['php']);

/**
 * @type {import('@vuepress/core').PluginObject}
 */
export const markdownPlugin = {
    name: 'markdown',
    extendsMarkdown: async md => {
        const rules = { ...md.renderer.rules };
        md.renderer.rules['heading_open'] = (tokens, idx, options, env, slf) => {
            const inlineCodes = tokens[idx + 1].children.filter(token => token.type === 'code_inline');
            inlineCodes.forEach(token => {
                token.meta = { heading: true };
            });
            return slf.renderToken(tokens, idx, options);
        }
        md.renderer.rules['code_inline'] = (tokens, idx, options, env, slf) => {
            if(tokens[idx].meta?.heading) {
                const token = tokens[idx];
                const highlighted = Prism.highlight(token.content, Prism.languages.php);
                const inlineNodes = tokens.filter(token =>
                    token.type === 'text' && token.content.trim()
                    || token.type === 'code_inline'
                );
                const isFullwidth = inlineNodes.length === 1;
                if(isFullwidth) {
                    return `<code class="inline full" v-pre>${highlighted}</code>`;
                }
            }
            return rules['code_inline'].call(null, tokens, idx, options, env, slf);
        };
    }
}
