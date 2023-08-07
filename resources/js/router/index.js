// import VueRouter from 'vue-router';
// import routes from "./routes";
// import { BASE_URL } from "../consts";
// import { parseQuery, stringifyQuery } from '../util/querystring';
//
// let currentRouter = null;
//
// export function router(fresh) {
//     if(!currentRouter || fresh) {
//         return currentRouter = new VueRouter({
//             mode: 'history',
//             routes,
//             base: `${BASE_URL}/`,
//             parseQuery,
//             stringifyQuery,
//         })
//     }
//     return currentRouter;
// }

// export function routeUrl(location, { append }={}) {
//     let { href, route } = router().resolve(location);
//     if(append) {
//         const currentPath = router().currentRoute.path.replace(/\/$/, '');
//         const resolved = router().resolve(currentPath + route.fullPath);
//         href = resolved.href;
//     }
//     return href;
// }

// export function getReferrerRoute() {
//     if(!document.referrer) {
//         return;
//     }
//     const referrerUrl = new URL(document.referrer);
//     if(referrerUrl.origin !== location.origin) {
//         return;
//     }
//     const { route } = router().resolve(referrerUrl.pathname + referrerUrl.search);
//     return route;
// }
