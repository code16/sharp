import { routeUrl } from 'sharp';
import ShowPage from './components/pages/ShowPage.vue';

export default [
    {
        name: 'show',
        path: '/show/:entityKey/:instanceId?',
        component: ShowPage,
    },
]

export function showUrl({ entityKey, instanceId }) {
    return routeUrl({
        name: 'show', params: { entityKey, instanceId }
    });
}