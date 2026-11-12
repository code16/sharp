import { usePage } from "@inertiajs/vue3";

export function getWeekStartsOn(mondayFirst: boolean) {
    return mondayFirst ? 0 : 6;
}

export function getDefaultDateLocale() {
    return usePage().props.locale;
}
