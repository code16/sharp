
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
