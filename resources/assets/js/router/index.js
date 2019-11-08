import VueRouter from 'vue-router';
import qs from 'qs';
import routes from "./routes";
import { BASE_URL } from "../consts";

let currentRouter = null;

export function router() {
    return currentRouter || (
        currentRouter = new VueRouter({
            mode: 'history',
            routes,
            base: `${BASE_URL}/`,
            parseQuery: query => qs.parse(query, { strictNullHandling: true }),
            stringifyQuery: query => qs.stringify(query, { addQueryPrefix: true, skipNulls: true }),
        })
    );
}