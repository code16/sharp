import Vue from 'vue/dist/vue.common';

import ActionView from './components/ActionView';
import Form from './components/form/Form';
import FieldDisplay from './components/form/FieldDisplay';
import EntitiesList from './components/list/EntitiesList';
import Dashboard from './components/dashboard/Dashboard';

import CollapsibleItem from './components/menu/CollapsibleItem';
import NavItem from './components/menu/NavItem';
import LeftNav from './components/menu/LeftNav';

import Loading from './components/Loading';

import axios from 'axios';
import cookies from 'axios/lib/helpers/cookies';

import * as qs from './helpers/querystring';


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
        [EntitiesList.name]:EntitiesList,
        [CollapsibleItem.name]:CollapsibleItem,
        [NavItem.name]:NavItem,
        [LeftNav.name]:LeftNav
    },

    created() {
        this.$on('setClass',(className,active)=> {
            //console.log('setClass', className, active);
            this.$el.classList[active ? 'add' : 'remove'](className);
        });
    }
});




