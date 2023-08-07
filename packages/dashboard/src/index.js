import dashboard from './store/dashboard';

export default function(Vue, { router, store }) {
    store.registerModule('dashboard', dashboard);
}
