import { usePage } from "@inertiajs/vue3";



export function __(key: string, replacements = null): string {
    const translation = usePage()?.props.translations[key] ?? key;

    return replace(translation, replacements);
}

// simpler version of https://github.com/conedevelopment/i18n/tree/master/resources/js
export function trans_choice(key: string, count: number = 1, replacements = null) {
    const translation = usePage()?.props.translations[key] ?? key;
    const chosenTranslation = count > 1 && translation.split('|').length > 1 ? translation.split('|')[1] : translation.split('|')[0];

    return replace(chosenTranslation ?? key, replacements);
}


function replace(translation: string, replacements: { [key:string]: string|number } | null) {
    if(replacements) {
        return Object.entries(replacements).reduce((res, [key, replacement]) => {
            return res.replace(`:${key}`, String(replacement));
        }, translation);
    }

    return translation;
}
