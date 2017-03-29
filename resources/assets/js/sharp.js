import Vue from 'vue';
import ElementUI from 'element-ui';
import Form from './components/Form';

Vue.use(ElementUI);


new Vue({
    el:"#app",
    components: {
        'sharp-form':Form
    }
});