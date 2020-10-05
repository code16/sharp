import Vue from 'vue';
import Check from '../../src/components/fields/Check.vue';


describe('check-field', () => {
    Vue.component('sharp-check', Check);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-check :value="true" :read-only="readOnly" @input="inputEmitted($event)"></sharp-check>
            </div>
        `
    });

    test('can mount Check field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "read only" Check field', async () => {
        await createVm({
            propsData: {
                readOnly: true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('emit event on checkbox changed & correct value', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            methods: {
                inputEmitted
            }
        });

        let checkbox = document.querySelector('input');
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledWith(true);
    });
});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [customOptions],

        props:['readOnly']
    });

    await Vue.nextTick();

    return vm.$children[0];
}