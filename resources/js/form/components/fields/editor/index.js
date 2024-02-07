import { Iframe } from "./extensions/iframe/iframe";
import { Link } from "@tiptap/extension-link";

export const defaultEditorOptions = {
    injectCSS: false,
    enableInputRules: false,
    enablePasteRules: [Iframe, Link],
}

export const editorProps = {
    id: String,
    value: {
        type: Object,
        default: ()=>({})
    },
    markdown: Boolean,
    locale: String,
    localized: Boolean,
    placeholder: String,
    toolbar: Array,
    minHeight: Number,
    maxHeight: Number,
    embeds: Object,
    inline: Boolean,
    uniqueIdentifier: String,
    showCharacterCount: Boolean,
    maxLength: Number,
}

export * from './extensions';
