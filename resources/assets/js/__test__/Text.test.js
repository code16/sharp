import Vue from 'vue/dist/vue';
import Text from '../components/form/fields/Text.vue';

import FakeInjections from './utils/FakeInjections';

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

    it('can mount Text field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('emit event on input', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            methods: {
                inputEmitted
            }
        });
        let input = document.querySelector('input');
        input.dispatchEvent(new InputEvent('input'));
        expect(inputEmitted.mock.calls.length).toBe(1);
    });

    it('emit correct value', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            methods: {
                inputEmitted
            }
        });
        let input = document.querySelector('input');
        input.dispatchEvent(new InputEvent('input'));
        expect(inputEmitted.mock.calls[0][0]).toBe('AAA');
    });

    it('take input type in account', async () => {
        await createVm({
            propsData: {
                inputType: 'password'
            }
        });
        let input = document.querySelector('input');
        expect(input.type === 'password');
    });

});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [FakeInjections, customOptions],

        props:['inputType']
    });

    await Vue.nextTick();

    return vm.$children[0];
}