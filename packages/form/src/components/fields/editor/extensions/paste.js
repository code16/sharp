import { Extension } from "@tiptap/core";
import { Plugin } from 'prosemirror-state';
import { DOMParser, Schema } from 'prosemirror-model';

export const Paste = Extension.create({
    name: 'paste',
    addOptions: () => ({
        schema: null,
        inline: false,
    }),
    addProseMirrorPlugins() {
        const schema = getNormalizedSchema(
            this.options.schema,
            this.editor.schema
        );
        const parser = DOMParser.fromSchema(schema);
        return [
            new Plugin({
                props: {
                    clipboardParser: parser,
                    clipboardTextParser: (text, $context) => {
                        if(this.options.inline) {
                            const dom = document.createElement('div');
                            dom.innerHTML = text.trim().replace(/(\r\n?|\n)/g, '<br>');
                            return parser.parseSlice(dom, {
                                preserveWhitespace: true,
                                context: $context,
                            });
                        }
                        return null;
                    },
                    transformPastedHTML: html => {
                        if(this.options.inline) {
                            return html
                                .replace(/<\/p>\s*<p[^>]*>/g, '<br><br>')
                                .replace(/<p[^>]*>/g, '')
                                .replace(/<\/p>/g, '');
                        }
                        return html;
                    },
                },
            })
        ]
    },
});

// needed to keep same references of node/mark types
function getNormalizedSchema(target, source) {
    const schema = new Schema(target.spec);
    schema.nodes = Object.fromEntries(
        Object.entries(source.nodes).filter(([key]) => !!target.nodes[key])
    );
    schema.marks = Object.fromEntries(
        Object.entries(source.marks).filter(([key]) => !!target.marks[key])
    );
    return schema;
}
