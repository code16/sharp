import { RawCommands } from "@tiptap/core";
import { EditorState } from "@tiptap/pm/state";

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        withoutHistory: {
            withoutHistory: (callback?: () => any) => ReturnType
        }
    }
}

export const withoutHistory: RawCommands['withoutHistory'] = (callback) => ({ tr, editor }) => {
    tr.setMeta('addToHistory', false);

    if(callback) {
        Object.defineProperty(editor.state, 'tr', {
            get: () => Object.getOwnPropertyDescriptor(EditorState.prototype, 'tr').get.call(editor.state)
                .setMeta('addToHistory', false),
            configurable: true,
        });
        tr.setMeta('preventDispatch', true);
        callback();
        Object.defineProperty(editor.state, 'tr', {
            get: () => Object.getOwnPropertyDescriptor(EditorState.prototype, 'tr').get.call(editor.state),
            configurable: true,
        });
    }

    return true;
}


