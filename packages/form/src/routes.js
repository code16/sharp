import FormPage from './components/pages/FormPage.vue';

export default [
    /** New spec */
    {
        name: 'form',
        path: '/(.*)?/s-form/:entityKey/:instanceId?',
        component: FormPage,
    }
]
