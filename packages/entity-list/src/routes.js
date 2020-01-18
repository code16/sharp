import { routeUrl } from 'sharp';
import EntityListPage from './components/pages/EntityListPage';

export default [
    {
        name: 'entity-list',
        path: '/list/:id',
        component: EntityListPage,
    },
]

export function listUrl(entityKey) {
    return routeUrl({
        name: 'entity-list', params: { id: entityKey },
    });
}