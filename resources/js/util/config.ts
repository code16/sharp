import get from "lodash/get";
import {usePage} from "@inertiajs/vue3";


export function config(key: string): any {
    return get(usePage().props.config, key);
}
