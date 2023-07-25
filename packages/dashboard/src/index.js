import dashboard from './store/dashboard';
import routes from './routes';

export default function(Vue, { router, store }) {
    store.registerModule('dashboard', dashboard);
    // router.addRoutes(routes);
}
