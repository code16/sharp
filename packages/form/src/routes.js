import FormPage from './components/pages/FormPage';

export default [
    /** New spec */
    {
        name: 'form',
        path: '/(.*)?/s-form/:entityKey/:instanceId?',
        component: FormPage,
    }
]
