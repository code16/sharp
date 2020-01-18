import show from './store/show';
import routes, { showUrl } from './routes';

export default function (Vue, { router, store }) {
    router.addRoutes(routes);
    store.registerModule('show', show);
}

export { 
    showUrl,
}