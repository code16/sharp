import { normalizeUrl } from "./url";
import { router } from "../router";
import { BASE_URL } from "../consts";


export function normalizeBreadcrumb(breadcrumb) {
    return breadcrumb?.map(item => ({
        ...item,
        url: normalizeUrl(item.url),
    }));
}

export function getBackUrl(breadcrumb) {
    const item = breadcrumb[breadcrumb.length - 2];
    return item ? normalizeUrl(item.url) : null;
}


function resolveRoute(breadcrumbUrl) {
    const parentUrl = new URL(breadcrumbUrl, location.origin);
    const path = parentUrl.pathname.replace(new RegExp(`^${BASE_URL}`), '');
    return router().resolve(path).route;
}

export function getDeleteBackUrl(breadcrumb) {
    const currentRoute = resolveRoute(breadcrumb[breadcrumb.length - 1].url);
    const parentUrl = breadcrumb[breadcrumb.length - 2].url;
    const parentRoute = resolveRoute(parentUrl);

    if(parentRoute.params.entityKey &&
        currentRoute.params.entityKey !== parentRoute.params.entityKey) {
        return normalizeUrl(parentUrl);
    }

    return normalizeUrl(breadcrumb[0].url);
}
