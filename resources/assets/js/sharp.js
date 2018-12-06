import './polyfill';
import Vue from 'vue';
import VueRouter from 'vue-router';
import { install as VueGoogleMaps } from 'vue2-google-maps';

import SharpActionView from './components/ActionView';
import SharpForm from './components/form/Form';
import SharpFieldDisplay from './components/form/field-display/FieldDisplay';
import SharpEntityList from './components/list/EntityList';
import SharpDashboard from './components/dashboard/Dashboard';

import SharpCollapsibleItem from './components/menu/CollapsibleItem';
import SharpNavItem from './components/menu/NavItem';
import SharpLeftNav from './components/menu/LeftNav';

import SharpItemVisual from './components/ui/ItemVisual';
import Loading from './components/ui/Loading';

import routes from './routes';

import axios from 'axios';
import cookies from 'axios/lib/helpers/cookies';

import * as qs from './helpers/querystring';

import Notifications from 'vue-notification';

Vue.use(Notifications);
Vue.use(VueGoogleMaps, {
    installComponents: false
});

// prevent recursive components import
Vue.component(SharpFieldDisplay.name, SharpFieldDisplay);
const SharpLoading = Vue.extend(Loading);

new Vue({
    el:"#sharp-app",

    provide: {
        mainLoading: new SharpLoading({ el: '#glasspane' }),
        xsrfToken: cookies.read(axios.defaults.xsrfCookieName),
        params: qs.parse()
    },

    components: {
        SharpActionView,
        SharpForm,
        SharpDashboard,
        SharpEntityList,
        SharpCollapsibleItem,
        SharpNavItem,
        SharpLeftNav,
        SharpItemVisual
    },

    created() {
        this.$on('setClass',(className,active)=> {
            //console.log('setClass', className, active);
            this.$el.classList[active ? 'add' : 'remove'](className);
        });
    },

    router: new VueRouter({ mode:'history', routes })
});




