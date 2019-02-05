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
    if(!obj || typeof obj !== 'object') return false;
    return locales.every(locale => locale in obj);
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

export function localeObjectOrEmpty({ localeObject, locale, value }) {
    let localizedValue = { ...localeObject, [locale]: value };
    return !isLocaleObjectEmpty(localizedValue) ? localizedValue: null;
}

export function resolveTextValue({ field, value }) {
    if(field.type === 'markdown' || field.type === 'wysiwyg') {
        return (value || {}).text;
    }
    return value;
}