const noop = ()=>{};

import { LocalizableFields, LocalizableOptionsFields, localeObject } from "../utils";

export function __createLocalizedValue({ locales, type, value }) {
    let textValue, create = noop;
    if(['text', 'textarea'].includes(type)) {
        textValue = value;
        create = val => val;
    }
    else if(['markdown', 'wysiwyg'].includes(type)) {
        textValue = value.text;
        create = val => ({ ...value, text:val });
    }
    else return value;

    return localeObject({ locales, resolve:l=>create(`${textValue} ${l.toUpperCase()}`) });
}

export function __createLocalizedOptions({ locales, options=[] }) {
    return options.map(option => ({
        ...option,
        label: localeObject({ locales, resolve:l=>`${option.label} ${l.toUpperCase()}` })
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
            ...res, [key]: typeof value === 'string' ? localeObject({ locales, resolve: l=>`${value} ${l.toUpperCase()}` }) : value
        }), {})
    );
}