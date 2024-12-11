import { Plugin, PluginKey, Transaction } from "@tiptap/pm/state";
import { Decoration, DecorationSet } from "@tiptap/pm/view";

export const PendingLinkPluginKey = new PluginKey<DecorationSet>('pendingLink');

export function pendingLink() {
    return new Plugin<DecorationSet>({
        key: PendingLinkPluginKey,
        state: {
            init: () => DecorationSet.empty,
            apply: (tr: Transaction, decorationSet: DecorationSet) => {
                const meta = tr.getMeta(PendingLinkPluginKey);

                if (meta != null) {
                    if(meta) {
                        return decorationSet.add(tr.doc, [
                            Decoration.inline(tr.selection.from, tr.selection.to, {
                                class: 'pending-link',
                            })
                        ]);
                    }

                    return DecorationSet.empty;
                }

                return decorationSet.map(tr.mapping, tr.doc);
            }
        },
        props: {
            decorations(state) {
                return PendingLinkPluginKey.getState(state);
            },
        },
    });
}
