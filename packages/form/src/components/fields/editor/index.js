import { Iframe } from "./extensions/iframe";

export const defaultEditorOptions = {
    injectCSS: false,
    enableInputRules: false,
    enablePasteRules: [Iframe],
}

export * from './extensions';
