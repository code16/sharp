import Vue from 'vue/dist/vue';
import Check from '../components/form/fields/Check.vue';


describe('check-field', () => {
    Vue.component('sharp-check', Check);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-check :value="true" read-only @input="inputEmitted($event)"></sharp-check>
            </div>
        `
    });

    it('can mount Check field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('emit event on checkbox changed', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            methods: {
                inputEmitted
            }
        });

        let checkbox = document.querySelector('input');
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
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
        input.dispatchEvent(new Event('change', { bubbles: true }));
        expect(inputEmitted.mock.calls[0][0]).toBe(true);
    });
});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [customOptions],

        props:['inputType']
    });

    await Vue.nextTick();

    return vm.$children[0];
}