export const LocalizableFields = [
    'text', 'markdown', 'textarea', 'wysiwyg', 'select', 'autocomplete', 'tags'
];
export const LocalizableOptionsFields = [
    'select', 'autocomplete', 'tags'
];
export const LocalizableValueFields = [
    'text', 'textarea', // localized value handled in Form/FieldDisplay components
    //'markdown', 'wysiwyg' (markdown/wysiwyg fields handle localized value themselves)
];

export function isLocaleObject(obj, locales) {
    return obj && typeof obj === 'object' && Object.keys(obj).every(key => locales.includes(key));
}

function isEmpty(value){
    return value === null || value === '';
}
export function isLocaleObjectEmpty(obj) {
    return Object.entries(obj).every(([locale, value])=>isEmpty(value));
}

export function isLocalizableValueField(field) {
    return LocalizableValueFields.includes(field.type);
}

export function localeObject({ locales, resolve=()=>null }) {
    return locales.reduce((res, locale)=>({
        ...res, [locale]: resolve(locale)
    }), {});
}

