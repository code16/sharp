import Vue from 'vue';
import ActionView from './components/ActionView';
import Form from './components/form/Form';
import FieldDisplay from './components/form/FieldDisplay';
import EntitiesList from './components/list/EntitiesList';
//import Dashboard from './components/dashboard/Dashboard';

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
        //[Dashboard.name]:Dashboard
    },

    mounted() {
        console.log(this);
    }
});




