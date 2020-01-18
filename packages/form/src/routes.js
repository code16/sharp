import { routeUrl } from 'sharp';
import FormPage from './components/pages/FormPage';

export default [
    {
        name: 'form',
        path: '/form/:entityKey/:instanceId?',
        component: FormPage,
    }
]

export function formUrl({ entityKey, instanceId }) {
    return routeUrl({
        name: 'form', params: { entityKey, instanceId },
    });
}
