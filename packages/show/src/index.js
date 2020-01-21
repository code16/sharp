import show from './store/show';
import routes from './routes';

export default function (Vue, { router, store }) {
    router.addRoutes(routes);
    store.registerModule('show', show);
}