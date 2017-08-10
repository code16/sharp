import Vue from 'vue/dist/vue';
import Check from '../components/form/fields/Check.vue';


describe('check-field', () => {
    Vue.component('sharp-check', Check);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-check :value="true" :read-only="readOnly" @input="inputEmitted($event)"></sharp-check>
            </div>
        `
    });

    it('can mount Check field', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount "read only" Check field', async () => {
        await createVm({
            propsData: {
                readOnly: true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('emit event on checkbox changed & correct value', async () => {
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