import { usePage } from "@inertiajs/vue3";


export function session(key: string) {
    return usePage().props.session[key];
}
