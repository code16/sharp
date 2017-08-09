import Vue from 'vue/dist/vue.common';
import Select from '../components/form/fields/Select.vue';

import { FakeI18n } from './utils';



describe('select-field',()=>{
    Vue.component('sharp-select', Select);
    Vue.use(FakeI18n);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-select :value="3" :multiple="multiple" :options="[{id:3,label:'AAA'}]" @input="inputEmitted($event)"></sharp-select>
            </div>
        `
    });

    it('can mount Select field', async () => {
        await createVm();
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('clear on cross button clicked', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            propsData: {
                multiple: false
            },
            methods: {
                inputEmitted
            }
        });

        let clearBtn = document.querySelector('.SharpSelect__clear-btn');
        clearBtn.dispatchEvent(new MouseEvent('mousedown'));

        expect(inputEmitted.mock.calls[0][0]).toBe(null);
    });

});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [customOptions],

        props:['multiple']
    });

    await Vue.nextTick();

    return vm.$children[0];
}