import {usePage} from "@inertiajs/vue3";


export function config(key: string): any {
    return usePage().props.config[key];
}
