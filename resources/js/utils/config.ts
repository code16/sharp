import {usePage} from "@inertiajs/vue3";


export function config(key: string): any {
    const props = usePage()?.props ?? JSON.parse(document.querySelector<HTMLElement>('#app').dataset.page).props;
    return props.config[key];
}
