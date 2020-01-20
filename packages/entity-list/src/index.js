import entityList from './store/entity-list';
import routes, { listUrl } from './routes';

export default function(Vue, { router, store }) {
    store.registerModule('entity-list', entityList);
    router.addRoutes(routes);
}

export {
    listUrl,
}

export * from './components';