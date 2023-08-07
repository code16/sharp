import { usePage } from "@inertiajs/vue3";

export function lang(key, defaultLabel) {
    const message = window.i18n?.[key];

    if(message) {
        return message;
    }

    if(defaultLabel !== undefined) {
        return defaultLabel;
    }

    return key;
}


export function __(key, replacements) {
    const translation = usePage()?.props.translations[key] ?? key;

    if(replacements) {
        return Object.entries(replacements).reduce((res, [key, replacement]) => {
            return res.replace(`:${key}`, replacement);
        }, translation);
    }

    return translation;
}
