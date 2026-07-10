import { Footnote, Footnotes as BaseFootnotes, FootnoteReference } from "tiptap-footnotes";
import { Plugin, PluginKey } from "@tiptap/pm/state";
import { Decoration, DecorationSet } from "@tiptap/pm/view";
import { Extension, mergeAttributes, NodePos } from "@tiptap/core";

export const Footnotes = Extension.create({
    name: 'footnotesExtension',
    addExtensions() {
        return [
            BaseFootnotes,
            Footnote.extend({
                addProseMirrorPlugins() {
                    const editor = this.editor;
                    return [
                        new Plugin({
                            key: new PluginKey('footnoteDecoration'),
                            props: {
                                decorations: (state) => {
                                    const decorations: Decoration[] = [];
                                    state.doc.descendants((node, pos) => {
                                        if (node.type.name === 'footnote') {
                                            const id = node.attrs.id;
                                            const refId = id?.startsWith('fn:') ? id.replace('fn:', '') : id;
                                            decorations.push(
                                                Decoration.widget(pos + 1, () => {
                                                    const sup = document.createElement('sup');
                                                    const a = document.createElement('a');
                                                    a.href = `#fnref:${refId}`;
                                                    a.draggable = false;
                                                    a.className = 'underline underline-offset-4 decoration-foreground/20 hover:decoration-foreground select-none';
                                                    a.textContent = '[^]';
                                                    sup.className = 'footnote-backref absolute left-[.125em] top-1.5'
                                                    sup.appendChild(a);
                                                    return sup;
                                                })
                                            );
                                        }
                                    });
                                    return DecorationSet.create(state.doc, decorations);
                                },
                                handleClickOn(view, pos, node, nodePos, event) {
                                    if((event.target as HTMLElement)?.closest('.footnote-backref')) {
                                        event.preventDefault();
                                        const id = (event.target as HTMLElement).closest('[data-id]').getAttribute('data-id');
                                        setTimeout(() => editor.commands.focusFootnoteReference(id));
                                        return true;
                                    }
                                },
                            },
                        }),
                    ];
                },
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
                    addCommands() {
                        return {
                            ...this.parent(),
                            focusFootnoteReference: (id: string) => ({ editor, chain }) => {
                                let matchedFootnoteReference: { from: number } = null;
                                editor.state.doc.descendants((node, pos) => {
                                    if (node.type.name === 'footnoteReference' && node.attrs['data-id'] === id) {
                                        matchedFootnoteReference = {
                                            from: pos,
                                        }
                                        return false;
                                    }
                                });

                                if (matchedFootnoteReference) {
                                    chain()
                                        .focus()
                                        .setTextSelection(
                                            matchedFootnoteReference.from,
                                        )
                                        .run();

                                    editor.view.dom.querySelector(`.footnote-ref[data-id="${id}"]`).scrollIntoView({ block: 'nearest' });
                                    return true;
                                }
                                return false;
                            },
                        };
                    },
                    addProseMirrorPlugins() {
                        const editor = this.editor;
                        return [
                            new Plugin({
                                key: new PluginKey("customFootnoteRefClick"),

                                props: {
                                    handleDOMEvents: {
                                        click(view, event) {
                                            if((event.target as HTMLElement)?.closest('.footnote-ref')) {
                                                event.preventDefault();
                                            }
                                        }
                                    },
                                    handleClickOn(view, pos, node, nodePos, event) {
                                        if(node.type.name === 'footnoteReference') {
                                            const id = node.attrs["data-id"];
                                            setTimeout(() => editor.commands.focusFootnote(id));
                                            return true;
                                        }
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

declare module "@tiptap/core" {
    interface Commands<ReturnType> {
        extendedFootnoteReference: {
            /**
             * scrolls to & sets the text selection at the end of the footnote with the given id
             * @param id the id of the footote (i.e. the `data-id` attribute value of the footnote)
             * @example editor.commands.focusFootnote("a43956c1-1ab8-462f-96e4-be3a4b27fd50")
             */
            focusFootnoteReference: (id: string) => ReturnType;
        };
    }
}
