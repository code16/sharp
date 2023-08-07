import show from './store/show';
import Field from './components/Field.vue';

export default function (Vue, { store }) {
    // router.addRoutes(routes);
    store.registerModule('show', show);
    Vue.component('ShowField', Field);
}
