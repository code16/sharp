import { Footnote, Footnotes as BaseFootnotes, FootnoteReference } from "tiptap-footnotes";
import { Plugin, PluginKey } from "@tiptap/pm/state";
import { Extension } from "@tiptap/core";

export const Footnotes = Extension.create({
    addExtensions() {
        return [
            BaseFootnotes,
            Footnote.extend({
                addCommands() {
                    return {
                        focusFootnote: (id: string) => ({ editor, chain }) => {
                            const matchedFootnote = editor.$node("footnote", {
                                "data-id": id,
                            });
                            if (matchedFootnote) {
                                // sets the text selection to the end of the footnote definition and scroll to it.
                                chain()
                                    .focus()
                                    .setTextSelection(
                                        matchedFootnote.from + matchedFootnote.content.size
                                    )
                                    .run();

                                matchedFootnote.element.scrollIntoView({ block: 'end' });
                                return true;
                            }
                            return false;
                        },
                    };
                },
            }),
            FootnoteReference
                .extend({
                    addProseMirrorPlugins() {
                        const editor = this.editor;
                        return [
                            new Plugin({
                                key: new PluginKey("customFootnoteRefClick"),

                                props: {
                                    handleDOMEvents: {
                                        click(view, event) {
                                            if(event.target?.closest('.footnote-ref')) {
                                                event.preventDefault();
                                            }
                                        }
                                    },
                                    handleClickOn(view, pos, node, nodePos, event) {
                                        // event.stopImmediatePropagation();
                                        const id = node.attrs["data-id"];
                                        console.log(event);
                                        setTimeout(() => editor.commands.focusFootnote(id));
                                    },
                                },
                            }),
                            ...this.parent(),
                        ]
                    },
                }),
        ];
    }
})
