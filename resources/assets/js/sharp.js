import Vue from 'vue';
import Form from './components/Form';
import FieldDisplay from './components/FieldDisplay';

import axios from 'axios';

Object.assign(window, { Vue, axios });

Vue.component(FieldDisplay.name, FieldDisplay);

new Vue({
    el:"#sharp-app",
    components: {
        [Form.name]:Form,
        //[List.name]:List
    }
});