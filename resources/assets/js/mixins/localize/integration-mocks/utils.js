import { LocalizableFields, LocalizableOptionsFields, localeObject } from "../utils";
const noop = ()=>{};

function __createLocalizedText(text) {
    return locale => `${text} ${locale.toUpperCase()}`;
}

export function __createLocalizedValue({ locales, type, value }) {
    if(['text', 'textarea'].includes(type) && typeof value === "string") {
        return localeObject({ locales, resolve:__createLocalizedText(value) });
    }
    else if(['markdown', 'wysiwyg'].includes(type) && typeof value.text === "string") {
        return {
            ...value,
            text: localeObject({ locales, resolve:__createLocalizedText(value.text) })
        };
    }
    return value;
}

export function __createLocalizedOptions({ locales, options=[] }) {
    return options.map(option => ({
        ...option,
        label: localeObject({ locales, resolve:__createLocalizedText(option.label) })
    }));
}

export function __createLocalizedField({ locales, field }) {
    if(LocalizableFields.includes(field.type)) {
        field.localized = true;
        if(LocalizableOptionsFields.includes(field.type)) {
            let options = field.type === 'autocomplete'? 'localValues': 'options';
            field[options] = __createLocalizedOptions({ locales, options: field[options] });
        }
    }
    else if(field.type === 'list') {
        field.itemFields = Object.entries(field.itemFields).reduce((res, [fieldKey, field]) => ({
            ...res, [fieldKey]: __createLocalizedField({ locales, field })
        }), {});
    }
    return field;
}

export function __createLocalizedAutocompleteSuggestions({ suggestions, locales }) {
    return suggestions.map(suggestion =>
        Object.entries(suggestion).reduce((res, [key, value])=>({
            ...res, [key]: typeof value === 'string' ? localeObject({ locales, resolve: __createLocalizedText(value) }) : value
        }), {})
    );
}