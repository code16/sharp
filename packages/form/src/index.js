import FieldDisplay from './components/FieldDisplay';
import routes from './routes';


export default function (Vue, { router, store }) {
    Vue.component('FieldDisplay', FieldDisplay);
    router.addRoutes(routes);
}

export * from './components';
