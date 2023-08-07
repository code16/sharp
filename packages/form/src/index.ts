import FieldDisplay from './components/FieldDisplay';
import routes from './routes';


export default function (Vue, { }) {
    Vue.component('FieldDisplay', FieldDisplay);
    // router.addRoutes(routes);
}

export * from './components';
