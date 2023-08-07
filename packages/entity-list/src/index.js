import entityList from './store/entity-list';

export default function(Vue, { store }) {
    store.registerModule('entity-list', entityList);
}

export * from './components';
export { entityList as entityListModule } from './store';
