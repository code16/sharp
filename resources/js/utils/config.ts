import { getInitialPageFromDOM } from "@inertiajs/core";
import type { Page} from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";


export function config(key: string): any {
    const props = usePage()?.props ?? getInitialPageFromDOM<Page>('app').props;
    return props.config[key];
}
