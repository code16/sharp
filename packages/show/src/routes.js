import ShowPage from './components/pages/ShowPage.vue';

export default [
    {
        name: 'show',
        path: '/show/:entityKey/:instanceId?',
        component: ShowPage,
    },
]