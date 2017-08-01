import Vue from 'vue';
import ActionView from './components/ActionView';
import Form from './components/form/Form';
import FieldDisplay from './components/form/FieldDisplay';
import EntitiesList from './components/list/EntitiesList';
//import Dashboard from './components/dashboard/Dashboard';

import CollapsibleItem from './components/menu/CollapsibleItem';
import NavItem from './components/menu/NavItem';

import SharpLoading from './components/Loading';

import axios from 'axios';
import cookies from 'axios/lib/helpers/cookies';

import * as qs from './helpers/querystring';


// prevent recursive components import
Vue.component(FieldDisplay.name, FieldDisplay);


new Vue({
    el:"#sharp-app",

    provide: {
        glasspane: new SharpLoading({ el: '#glasspane' }),
        xsrfToken: cookies.read(axios.defaults.xsrfCookieName),
        params: qs.parse()
    },

    components: {
        [ActionView.name]:ActionView,
        [Form.name]:Form,
        [EntitiesList.name]:EntitiesList,
        [CollapsibleItem.name]:CollapsibleItem,
        [NavItem.name]:NavItem
    },

    mounted() {
    }
});




