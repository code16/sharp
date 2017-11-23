import Vue from 'vue';
import Text from '../components/form/fields/Text.vue';

import { MockInjections } from './utils';

describe('text-field', () => {
    Vue.component('sharp-text', Text);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-text :input-type="inputType" 
                            placeholder="Entrez du texte" 
                            value="AAA" read-only 
                            @input="inputEmitted($event)">
                </sharp-text>
            </div>
        `
    });

    test('can mount Text field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('emit event on input and correct value', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            methods: {
                inputEmitted
            }
        });
        let input = document.querySelector('input');
        input.dispatchEvent(new Event('input', { bubbles:true }));
        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledWith('AAA');
    });

    test('take input type in account', async () => {
        await createVm({
            propsData: {
                inputType: 'password'
            }
        });
        let input = document.querySelector('input');
        expect(input.type).toBe('password');
    });

});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [MockInjections, customOptions],

        props:['inputType']
    });

    await Vue.nextTick();

    return vm.$children[0];
}