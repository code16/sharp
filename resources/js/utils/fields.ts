import { logError } from "./log";

const customFieldRE = /^custom-(.+)$/;

export function isCustomField(type) {
    return customFieldRE.test(type);
}

export function resolveCustomField(type) {
    const [_, name] = type.match(customFieldRE) || [];
    const component = name
        ? window.sharpPlugin?.customFields?.[name]
        : null;
    if(!component) {
        logError(`unknown custom field type '${type}', make sure you register it correctly (https://sharp.code16.fr/docs/guide/custom-form-fields.html#register-the-custom-field)`);
    }
    return component;
}
