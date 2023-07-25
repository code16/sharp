import globalFilters from './store/global-filters';

export default function(Vue, { store }) {
    store.registerModule('global-filters', globalFilters);

    Vue.component('sharp-global-filters', () => import('./components/GlobalFilters'));
}

export * from './components';
export { filters as filtersModule } from './store';
