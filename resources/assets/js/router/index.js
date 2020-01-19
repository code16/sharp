import VueRouter from 'vue-router';
import qs from 'qs';
import routes from "./routes";
import { BASE_URL } from "../consts";

let currentRouter = null;

export function stringifyQuery(query) {
    return qs.stringify(query, { addQueryPrefix: true, skipNulls: true });
}

export function parseQuery(query) {
    return qs.parse(query, { ignoreQueryPrefix: true, strictNullHandling: true });
}

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