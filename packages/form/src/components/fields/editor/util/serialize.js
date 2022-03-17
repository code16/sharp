import { elementFromString } from "./dom";
import { serializeEmbedElement } from "../extensions/embed/util";

function serializeHTML(html) {
    const dom = elementFromString(html ?? '');

    dom.querySelectorAll('*').forEach(el => {
        serializeEmbedElement(el);
    });

    return dom.innerHTML;
}

export function serializeEditorPostValue(value) {
    if(!value || !value.text) {
        return value;
    }

    if(typeof value.text === 'object') {
        return {
            ...value,
            text: Object.fromEntries(
                Object.entries(value.text).map(([key, localizedValue]) => [
                    key,
                    serializeHTML(localizedValue)
                ])
            ),
        }
    }

    return {
        ...value,
        text: serializeHTML(value.text),
    };
}
