import { Mark, mergeAttributes } from "@tiptap/core";

export const Small = Mark.create({
    name: 'small',

    parseHTML() {
        return [
            {
                tag: 'small',
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['small', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes), 0]
    },

    addCommands() {
        return {
            toggleSmall: () => ({ commands }) => {
                return commands.toggleMark(this.name)
            },
        }
    },

    addKeyboardShortcuts() {
        return {
            'Mod-Shift-s': () => this.editor.commands.toggleSmall(),
        }
    },
});

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        small: {
            toggleSmall: () => ReturnType
        }
    }
}
