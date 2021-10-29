import { Iframe } from "../extensions/iframe";


export function disablePasteRules(extension) {
    if(extension.name === Iframe.name) {
        return extension;
    }
    return extension.extend({
        addPasteRules: null,
    });
}
