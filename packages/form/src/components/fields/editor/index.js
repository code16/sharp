import { Iframe } from "./extensions/iframe";

export const defaultEditorOptions = {
    injectCSS: false,
    enableInputRules: false,
    enablePasteRules: [Iframe],
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
    uniqueIdentifier: String,
    fieldConfigIdentifier: String,
}

export * from './extensions';
