import './polyfill';
// import './dev';
import { createApp, h, nextTick } from 'vue';
import Vuex from 'vuex';
// import VueRouter from 'vue-router';
// import { install as VueGoogleMaps } from './vendor/vue2-google-maps';
// import Notifications from 'vue-notification';

import SharpCommands from '@sharp/commands';
import SharpDashboard from '@sharp/dashboard';
import SharpEntityList from '@sharp/entity-list';
import SharpFilters from '@sharp/filters';
import SharpForm from '@sharp/form';
import SharpShow from '@sharp/show';
import SharpUI from '@sharp/ui/src';
import SharpSearch from '@sharp/search';

import ActionView from './components/ActionView.vue';
import LeftNav from './components/LeftNav.vue';
import {
    NavSection,
    NavItem,
} from '@sharp/ui/src';

import { store as getStore } from './store/store';
// import { router as getRouter } from "./router";
import { createInertiaApp } from "@inertiajs/vue3";
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ignoredElements } from "./util/vue";


// Vue.use(VueGoogleMaps, {
//     installComponents: false
// });

// const router = getRouter();
const store = getStore();


// Vue.component('sharp-action-view', ActionView);
// Vue.component('sharp-left-nav', LeftNav);
// Vue.component('sharp-nav-section', NavSection);
// Vue.component('sharp-nav-item', NavItem);

if(document.querySelector('[data-page]')) {
    createInertiaApp({
        resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
        setup({ el, App, props, plugin }) {
            const app = createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, Ziggy)
                .use(store);

            app.config.compilerOptions.isCustomElement = tag => ignoredElements.includes(tag);

            app.use(SharpCommands, { store });
            app.use(SharpDashboard, { store });
            app.use(SharpEntityList, { store });
            app.use(SharpFilters, { store });
            app.use(SharpForm, { store });
            app.use(SharpShow, { store });
            app.use(SharpUI, { store });
            app.use(SharpSearch, { store });

            app.mount(el);

            nextTick(() => {
                window.dispatchEvent(new CustomEvent('sharp:mounted'));
            });
        },
    })
} else {
    // new Vue({
    //     store,
    //     router,
    // }).$mount('#app');
}





