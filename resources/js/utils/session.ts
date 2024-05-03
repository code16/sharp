import { usePage } from "@inertiajs/vue3";
import { SessionData } from "@/types";


export function session<K extends keyof SessionData>(key: K): SessionData[K] {
    return usePage().props.session[key];
}
