import DashboardPage from './components/pages/DashboardPage.vue';

export default [
    /** New spec */
    {
        name: 'dashboard',
        path: '/s-dashboard/:dashboardKey',
        component: DashboardPage,
    },
]
