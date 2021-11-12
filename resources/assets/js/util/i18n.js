
export function lang(key, defaultLabel) {
    return window.i18n?.[key]
        ?? defaultLabel
        ?? key;
}
