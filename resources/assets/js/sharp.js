import './polyfill';
import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router';
import { install as VueGoogleMaps } from 'vue2-google-maps';

import SharpActionView from './components/ActionView';
import SharpForm from './components/form/Form';
import SharpFieldDisplay from './components/form/field-display/FieldDisplay';

import SharpCollapsibleItem from './components/menu/CollapsibleItem';
import SharpNavItem from './components/menu/NavItem';
import SharpLeftNav from './components/menu/LeftNav';

import SharpItemVisual from './components/ui/ItemVisual';
import Loading from './components/ui/Loading';

import routes from './routes';

import axios from 'axios';
import cookies from 'axios/lib/helpers/cookies';
import qs from 'qs';

import Notifications from 'vue-notification';

import store from './store';
import { BASE_URL } from "./consts";

Vue.use(Notifications);
Vue.use(VueGoogleMaps, {
    installComponents: false
});

Vue.config.ignoredElements = [/^trix-/];

// prevent recursive components import
Vue.component(SharpFieldDisplay.name, SharpFieldDisplay);
const SharpLoading = Vue.extend(Loading);

new Vue({
    el:"#sharp-app",

    provide: {
        mainLoading: new SharpLoading({ el: '#glasspane' }),
        xsrfToken: cookies.read(axios.defaults.xsrfCookieName)
    },

    components: {
        SharpActionView,
        SharpForm,
        SharpCollapsibleItem,
        SharpNavItem,
        SharpLeftNav,
        SharpItemVisual
    },

    created() {
        this.$on('setClass',(className,active)=> {
            this.$el.classList[active ? 'add' : 'remove'](className);
        });
    },

    store: new Vuex.Store(store),
    router: new VueRouter({
        mode: 'history',
        routes,
        base: `${BASE_URL}/`,
        parseQuery: query => qs.parse(query, { strictNullHandling: true }),
        stringifyQuery: query => qs.stringify(query, { addQueryPrefix: true, skipNulls: true }),
    })
});




