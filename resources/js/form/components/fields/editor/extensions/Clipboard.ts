import { Command, Extension } from "@tiptap/core";
import { Plugin } from '@tiptap/pm/state';
import { DOMParser, Schema } from '@tiptap/pm/model';
import { __ } from "@/utils/i18n";

export const Clipboard = Extension.create({
    name: 'clipboard',
    addOptions() {
        return {
            schema: null,
            inline: false,
        }
    },
    addProseMirrorPlugins() {
        const schema = getNormalizedSchema(
            this.options.schema,
            this.editor.schema
        );
        const parser = DOMParser.fromSchema(schema);
        return [
            new Plugin({
                props: {
                    // clipboardParser: parser,
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
    addCommands() {
        return {
            copyNode: (pos: number): Command => ({ editor, dispatch }) => {
                if(dispatch) {
                    editor.commands.setNodeSelection(pos);
                    const clipboardData = new DataTransfer();
                    const event = new ClipboardEvent('copy', {
                        bubbles: true,
                        cancelable: true,
                        clipboardData,
                    });

                    editor.view.dom.dispatchEvent(event);

                    const clipboardItem = new ClipboardItem({
                        'text/html': clipboardData.getData('text/html'),
                        'text/plain': clipboardData.getData('text/plain'),
                    });

                    navigator.clipboard.write([clipboardItem]).then(() => {
                    }).catch(err => {
                        alert(__('sharp::errors.failed_to_write_to_clipboard'));
                    });
                }

                return true;
            },
        }
    }
});

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        'clipboard': {
            copyNode: (pos: number) => ReturnType
        }
    }
}

// needed to keep same references of node/mark types
function getNormalizedSchema(target, source) {
    const schema = new Schema(target.spec);
    // @ts-ignore
    schema.nodes = Object.fromEntries(
        Object.entries(source.nodes).filter(([key]) => !!target.nodes[key])
    );
    // @ts-ignore
    schema.marks = Object.fromEntries(
        Object.entries(source.marks).filter(([key]) => !!target.marks[key])
    );
    return schema;
}
