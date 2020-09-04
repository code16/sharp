import show from './store/show';
import routes from './routes';
import Field from './components/Field';

export default function (Vue, { router, store }) {
    router.addRoutes(routes);
    store.registerModule('show', show);
    Vue.component('ShowField', Field);
}