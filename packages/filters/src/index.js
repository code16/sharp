import globalFilters from './store/global-filters';
import { GlobalFilters } from './components';

export default function(Vue, { store }) {
    store.registerModule('global-filters', globalFilters);

    Vue.component('sharp-global-filters', GlobalFilters);
}

export * from './components';
export { filters as filtersModule } from './store';
