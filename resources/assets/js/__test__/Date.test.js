import Vue from 'vue';
import Date from '../components/form/fields/date/Date.vue';


describe('data-field',()=>{
    Vue.component('sharp-date', Date);
});

async function createVm() {
    let vm = new Vue({
        el: '#app',

        props: ['readOnly']

    });

    await Vue.nextTick();

    return vm.$children[0];
}