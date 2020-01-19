import './polyfill';
import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router';
import { install as VueGoogleMaps } from 'vue2-google-maps';

import SharpCommands from 'sharp-commands';
import SharpDashboard from 'sharp-dashboard';
import SharpEntityList from 'sharp-entity-list';
import SharpFilters from 'sharp-filters';
import SharpForm from 'sharp-form';
import SharpShow from 'sharp-show';
import SharpUI from 'sharp-ui';

import ActionView from './components/ActionView';

import CollapsibleItem from './components/menu/CollapsibleItem';
import NavItem from './components/menu/NavItem';
import LeftNav from './components/menu/LeftNav';

import ItemVisual from './components/ui/ItemVisual';
import Loading from './components/ui/Loading';

import { router as getRouter } from "./router";

import axios from 'axios';
import cookies from 'axios/lib/helpers/cookies';
import Notifications from 'vue-notification';

import locale from 'element-ui/lib/locale';
import { elLang } from './util/element-ui';

locale.use(elLang());

Vue.use(Notifications);
Vue.use(VueGoogleMaps, {
    installComponents: false
});

Vue.use(VueRouter);
Vue.use(Vuex);

const store = new Vuex.Store();
const router = getRouter();

Vue.use(SharpCommands, { store, router });
Vue.use(SharpDashboard, { store, router });
Vue.use(SharpEntityList, { store, router });
Vue.use(SharpFilters, { store, router });
Vue.use(SharpForm, { store, router });
Vue.use(SharpShow, { store, router });
Vue.use(SharpUI, { store, router });


Vue.config.ignoredElements = [/^trix-/];

const SharpLoading = Vue.extend(Loading);

new Vue({
    el:"#sharp-app",

    provide: {
        mainLoading: new SharpLoading({ el: '#glasspane' }),
        xsrfToken: cookies.read(axios.defaults.xsrfCookieName)
    },

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
                ...this.$route,
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




