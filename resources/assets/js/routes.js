import EntityListPage from './components/pages/EntityListPage.vue';
import DashboardPage from './components/pages/DashboardPage.vue';

export default [
    {
        name: 'entity-list',
        path: '/list/:id',
        component: EntityListPage
    },
    {
        name: 'dashboard',
        path: '/dashboard/:id',
        component: DashboardPage,
    }
];