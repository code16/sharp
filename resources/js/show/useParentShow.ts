import { inject } from "vue";
import { Show } from "@/show/Show";


export function useParentShow() {
    return inject<Show>('show');
}
