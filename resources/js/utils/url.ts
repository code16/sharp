import routeFn from 'ziggy-js';
import { config } from "@/utils/config";

export function getAppendableUri() {
    return location.pathname.replace(new RegExp(`^/${config('sharp.custom_url_segment')}/`), '');
}

export const route = routeFn;
