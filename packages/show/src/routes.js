import ShowPage from './components/pages/ShowPage.vue';

export default [
    /** New spec */
    {
        name: 'show',
        path: '/(.*)?/s-show/:entityKey/:instanceId?',
        component: ShowPage,
    },
]
