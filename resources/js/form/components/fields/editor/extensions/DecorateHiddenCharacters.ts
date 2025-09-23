import { Extension } from "@tiptap/core";
import { Decoration, DecorationSet } from "@tiptap/pm/view";
import { Plugin, EditorState } from "@tiptap/pm/state";

type Key = "nbsp" | "shy";

type Options = {
    class?: string;
    characters?: {
        [key in Key]: { class: string };
    };
};

export const DecorateHiddenCharacters = Extension.create<Options>({
    name: "decorateHiddenCharacters",

    addOptions() {
        return {};
    },

    addProseMirrorPlugins() {
        const buildCharDecorations = (
            state: EditorState,
            { char, key }: { char: string, key: Key }
        ) => {
            const decorations: Decoration[] = [];

            state.doc.descendants((node, pos) => {
                if (!node.isText) return true;

                const text = node.text;
                if (!text) return true;

                let idx = text.indexOf(char);
                while (idx !== -1) {
                    const from = pos + idx;
                    const to = from + 1;

                    const isSelected =
                        (from >= state.selection.from && from < state.selection.to) ||
                        (to > state.selection.from && to <= state.selection.to);

                    const deco = Decoration.inline(from, to, {
                        'data-key': key,
                        'data-selected': isSelected ? 'true' : null,
                        class: [
                            this.options.class,
                            this.options.characters?.[key]?.class,
                        ]
                            .filter(Boolean)
                            .join(' '),
                    });
                    decorations.push(deco);

                    idx = text.indexOf(char, idx + 1);
                }

                return true;
            });

            return decorations;
        };

        const buildDecorations = (state: EditorState) => {
            return DecorationSet.create(state.doc, [
                ...buildCharDecorations(state, { char: '\u00A0', key: 'nbsp' }),
                ...buildCharDecorations(state, { char: '\u00AD', key: 'shy' }),
            ]);
        };

        return [
            new Plugin({
                state: {
                    init(_, state) {
                        return buildDecorations(state);
                    },
                    apply(tr, old, oldState, newState) {
                        if (!tr.docChanged && !tr.selectionSet) return old;
                        return buildDecorations(newState);
                    },
                },
                props: {
                    decorations(state) {
                        return this.getState(state);
                    },
                },
            }),
        ];
    },
});
