import { Link as BaseLink, LinkOptions } from "@tiptap/extension-link";
import { pendingLink, PendingLinkPluginKey } from "@/form/components/fields/editor/extensions/link/pending-link";
import { Extension } from "@tiptap/core";


export const Link = Extension.create<LinkOptions>({
    name: 'sharpLink',
    addExtensions() {
        return [
            BaseLink.configure(this.options).extend({
                addCommands() {
                    return {
                        ...this.parent(),
                        togglePendingLink: (show: boolean) => ({ tr }) => {
                            tr.setMeta(PendingLinkPluginKey, show);
                            return !tr.selection.empty;
                        },
                    }
                },
                addProseMirrorPlugins() {
                    return [
                        ...this.parent(),
                        pendingLink(),
                    ];
                }
            }),
        ];
    },
});

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        sharpLink: {
            togglePendingLink: (show: boolean) => ReturnType,
        },
    }
}
