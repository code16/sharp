import Vue from 'vue';
import Textarea from '../../src/components/fields/Textarea.vue';

describe('textarea-field',()=>{
    Vue.component('sharp-textarea', Textarea);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-textarea placeholder="Entrez du texte" :rows="30" read-only value="AAA" @input="inputEmitted($event)"></sharp-textarea>
            </div>
        `
    });

    test('can mount Textarea field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('emit event on input', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            methods: {
                inputEmitted
            }
        });
        let input = document.querySelector('textarea');
        input.dispatchEvent(new Event('input', { bubbles: true }));
        expect(inputEmitted.mock.calls.length).toBe(1);
    });

    test('emit correct value', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            methods: {
                inputEmitted
            }
        });
        let input = document.querySelector('textarea');
        input.dispatchEvent(new Event('input', { bubbles: true }));
        expect(inputEmitted.mock.calls[0][0]).toBe('AAA');
    });
});


async function createVm(customOptions={}) {
    const vm = new Vue({
        el: '#app',
        mixins: [customOptions],
    });

    await Vue.nextTick();

    return vm.$children[0];
}