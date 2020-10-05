import VueRouter from 'vue-router';
import routes from "./routes";
import { BASE_URL } from "../consts";
import { normalizeUrl } from '../util/url';
import { parseQuery, stringifyQuery } from '../util/querystring';

let currentRouter = null;

export function router(fresh) {
    if(!currentRouter || fresh) {
        return currentRouter = new VueRouter({
            mode: 'history',
            routes,
            base: `${BASE_URL}/`,
            parseQuery,
            stringifyQuery,
        })
    }
    return currentRouter;
}

export function routeUrl(location, { normalized=true }={}) {
    const { href } = router().resolve(location);
    return normalized ? normalizeUrl(href) : href;
}