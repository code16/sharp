import Vue from 'vue';
import NumberInput from '../components/form/fields/Number.vue';

import { MockInjections } from './utils';


describe('number-field',()=>{
    Vue.component('sharp-number', NumberInput);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-number placeholder="Entrez un nombre" value="1" :show-controls="false" step="1" min="0" max="10"></sharp-number>
            </div>
        `
    });

    test('can mount Number field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });
});


async function createVm(customOptions={}) {
    const vm = new Vue({
        el: '#app',
        mixins: [MockInjections, customOptions],
    });

    await Vue.nextTick();

    return vm.$children[0];
}