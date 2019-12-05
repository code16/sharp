import './polyfill';
import Vue from 'vue';
import Vuex from 'vuex';
import { install as VueGoogleMaps } from 'vue2-google-maps';

import SharpActionView from './components/ActionView';
import SharpFieldDisplay from './components/form/field-display/FieldDisplay';

import SharpCollapsibleItem from './components/menu/CollapsibleItem';
import SharpNavItem from './components/menu/NavItem';
import SharpLeftNav from './components/menu/LeftNav';

import SharpItemVisual from './components/ui/ItemVisual';
import Loading from './components/ui/Loading';

import { router } from "./router";

import axios from 'axios';
import cookies from 'axios/lib/helpers/cookies';
import Notifications from 'vue-notification';

import store from './store';

import locale from 'element-ui/lib/locale';
import { elLang } from './util/element-ui';

locale.use(elLang());

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
        SharpCollapsibleItem,
        SharpNavItem,
        SharpLeftNav,
        SharpItemVisual
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

    store: new Vuex.Store(store),
    router: router(),
});




