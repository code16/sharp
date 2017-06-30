import Vue from 'vue';
import Form from './components/form/Form';
import FieldDisplay from './components/form/FieldDisplay';
import ActionView from './components/ActionView';
import Dashboard from './components/dashboard/Dashboard';

import SharpLoading from './components/Loading';

import axios from 'axios';
import cookies from 'axios/lib/helpers/cookies';

// prevent recursive components import
Vue.component(FieldDisplay.name, FieldDisplay);

new Vue({
    el:"#sharp-app",

    provide: {
        glasspane: new SharpLoading({ el: '#glasspane' }),
        xsrfToken: cookies.read(axios.defaults.xsrfCookieName),
    },

    components: {
        [ActionView.name]:ActionView,
        [Form.name]:Form,
        [Dashboard.name]:Dashboard
    },
});




