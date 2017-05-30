import Vue from 'vue';
import Form from './components/form/Form';
import FieldDisplay from './components/form/FieldDisplay';
import ActionView from './components/ActionView';

import axios from 'axios';

Object.assign(window, { Vue, axios });

// prevent recursive components import
Vue.component(FieldDisplay.name, FieldDisplay);


new Vue({
    el:"#sharp-app",
    components: {
        [ActionView.name]:ActionView,
        [Form.name]:Form,
        //[List.name]:List
    }
});