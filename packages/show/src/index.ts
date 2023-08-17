import show from './store/show';

export default function (Vue, { store }) {
    // router.addRoutes(routes);
    store.registerModule('show', show);
}

export { Show } from './Show';
