import './polyfill';
import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router';
import Trix from 'trix';
import { install as VueGoogleMaps } from 'vue2-google-maps';
import Notifications from 'vue-notification';
import locale from 'element-ui/lib/locale';
import { elLang } from './util/element-ui';


import SharpCommands from 'sharp-commands';
import SharpDashboard from 'sharp-dashboard';
import SharpEntityList from 'sharp-entity-list';
import SharpFilters from 'sharp-filters';
import SharpForm from 'sharp-form';
import SharpShow from 'sharp-show';
import SharpUI from 'sharp-ui';

import ActionView from './components/ActionView';
import LeftNav from './components/LeftNav';
import {
    ItemVisual,
    CollapsibleItem,
    NavItem,
} from 'sharp-ui';

import { store as getStore } from './store/store';
import { router as getRouter } from "./router";

locale.use(elLang());

Vue.use(Notifications);
Vue.use(VueGoogleMaps, {
    installComponents: false
});

Vue.use(VueRouter);
Vue.use(Vuex);

const router = getRouter();
const store = getStore();

Vue.use(SharpCommands, { store, router });
Vue.use(SharpDashboard, { store, router });
Vue.use(SharpEntityList, { store, router });
Vue.use(SharpFilters, { store, router });
Vue.use(SharpForm, { store, router });
Vue.use(SharpShow, { store, router });
Vue.use(SharpUI, { store, router });

window.Trix = Trix;
Vue.config.ignoredElements = [/^trix-/];


new Vue({
    el: "#sharp-app",

    components: {
        'sharp-action-view': ActionView,
        'sharp-left-nav': LeftNav,
        'sharp-collapsible-item': CollapsibleItem,
        'sharp-nav-item': NavItem,
        'sharp-item-visual': ItemVisual
    },

    created() {
        this.$on('setClass',(className,active)=> {
            this.$el.classList[active ? 'add' : 'remove'](className);
        });
        if(this.$route.query['x-access-from']) {
            this.$router.replace({
                path: this.$route.path,
                query: {
                    ...this.$route.query,
                    'x-access-from': undefined
                }
            });
        }
    },

    store,
    router,
});




