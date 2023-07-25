import entityList from './store/entity-list';
import routes from './routes';

export default function(Vue, { store }) {
    store.registerModule('entity-list', entityList);
    // router.addRoutes(routes);
}

export * from './components';
export { entityList as entityListModule } from './store';
