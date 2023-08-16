import {Page, router} from "@inertiajs/core";
import { Config, RouteParam, RouteParamsWithQueryOverload, Router } from 'ziggy-js';

// interface SharedProps {
//     auth: {
//         user: App.Data.UserData,
//     },
//     can: App.Data.UserAuthorizationsData,
//     session: {
//         status: string | null
//     },
//     notifications: App.Data.NotificationsData,
//     currentPlatinaNetwork: App.Data.PlatinaNetworkData,
// }
//
// declare module '@inertiajs/vue3' {
//     export function usePage(): Page<SharedProps>
// }
//
// declare module '@vue/runtime-core' {
//     export interface ComponentCustomProperties {
//         $inertia: typeof router;
//         $page: Page<SharedProps>;
//     }
// }


export type ZiggyRouter = Router & { get params(): { [key: string]: string } }

declare global {
    function route(
        name?: undefined,
        params?: RouteParamsWithQueryOverload | RouteParam,
        absolute?: boolean,
        config?: Config,
    ): ZiggyRouter;
    function route(
        name: string,
        params?: RouteParamsWithQueryOverload | RouteParam,
        absolute?: boolean,
        config?: Config,
    ): string;
}

interface PaginatorMeta {
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    next_page_url: string | null | undefined;
    path: string;
    per_page: number;
    prev_page_url: string | null | undefined;
    to: number | null;
    total: number;
}

export type PaginationLinks = Array<{ url:string, label:string, active:boolean }>;

export type Paginator<T> = {
    data: T,
    links: PaginationLinks,
    meta: PaginatorMeta,
}
