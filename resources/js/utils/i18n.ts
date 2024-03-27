import { usePage } from "@inertiajs/vue3";



export function __(key: string, replacements = null): string {
    const translation = usePage()?.props.translations[key] ?? key;

    if(replacements) {
        return Object.entries(replacements).reduce((res, [key, replacement]) => {
            return res.replace(`:${key}`, replacement);
        }, translation);
    }

    return translation;
}
