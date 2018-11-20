import './polyfill';
import Vue from 'vue';
import { install as VueGoogleMaps } from 'vue2-google-maps';

import ActionView from './components/ActionView';
import Form from './components/form/Form';
import FieldDisplay from './components/form/field-display/FieldDisplay';
import EntityList from './components/list/EntityList';
import Dashboard from './components/dashboard/Dashboard';

import CollapsibleItem from './components/menu/CollapsibleItem';
import NavItem from './components/menu/NavItem';
import LeftNav from './components/menu/LeftNav';

import Loading from './components/ui/Loading';
import ItemVisual from './components/ui/ItemVisual';

import axios from 'axios';
import cookies from 'axios/lib/helpers/cookies';

import * as qs from './helpers/querystring';

import Notifications from 'vue-notification';

Vue.use(Notifications);
Vue.use(VueGoogleMaps, {
    installComponents: false
});

// prevent recursive components import
Vue.component(FieldDisplay.name, FieldDisplay);
const SharpLoading = Vue.extend(Loading);

new Vue({
    el:"#sharp-app",

    provide: {
        mainLoading: new SharpLoading({ el: '#glasspane' }),
        xsrfToken: cookies.read(axios.defaults.xsrfCookieName),
        params: qs.parse()
    },

    components: {
        [ActionView.name]:ActionView,
        [Form.name]:Form,
        [Dashboard.name]:Dashboard,
        [EntityList.name]:EntityList,
        [CollapsibleItem.name]:CollapsibleItem,
        [NavItem.name]:NavItem,
        [LeftNav.name]:LeftNav,
        [ItemVisual.name]:ItemVisual
    },

    created() {
        this.$on('setClass',(className,active)=> {
            //console.log('setClass', className, active);
            this.$el.classList[active ? 'add' : 'remove'](className);
        });
    }
});




