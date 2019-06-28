import Vue from 'vue';
import Select from '../components/form/fields/Select.vue';

import { MockI18n } from './test-utils';



describe('select-field',()=>{
    Vue.use(MockI18n);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-select :value="value" 
                              :multiple="multiple" 
                              :display="display" 
                              :options="[{id:3,label:'AAA'}, {id:4, label:'BBB'}]" 
                              :read-only="readOnly"
                              :max-selected="multiple ? 3 : undefined"
                              placeholder="placeholder"
                              unique-identifier="select"
                              clearable
                              @input="inputEmitted($event)">
                </sharp-select>
            </div>
        `;
        MockI18n.mockLangFunction();
    });

    test('can mount Select field', async () => {
        await createVm({
            data:() => ({ value: null }) // possible String/Number value (no reactive)
        });
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount Select field with clear button', async () => {
        await createVm({
            propsData: {
                multiple: false
            },
            data:() => ({ value: 3 }) // possible String/Number value (no reactive)
        });
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount Select field as checkboxes list', async () => {
        await createVm({
            propsData: {
                multiple: true,
                display: 'list'
            },
            data: () => ({ value: [] })
        });
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "read only" Select field as checkboxes list', async () => {
        await createVm({
            propsData: {
                multiple: true,
                display: 'list',
                readOnly: true,
            },
            data:() => ({ value: [] })
        });
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount Select field as radios list', async () => {
        await createVm({
            propsData: {
                multiple: false,
                display: 'list'
            },
            data: () => ({ value: null })
        });
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "read only" Select field as radios list', async () => {
        await createVm({
            propsData: {
                multiple: false,
                display: 'list',
                readOnly: true
            },
            data: () => ({ value: null })
        });
        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('expose appropriate props to multiselect component', async () => {

        let $tags = await createVm({
            propsData: {
                multiple: true, readOnly: true
            },
            data: () => ({ value: [3] })
        });

        let { multiselect } = $tags.$refs;

        expect(multiselect.$props).toMatchObject({
            value: [3],
            placeholder: 'placeholder',
            max: 3,
            disabled: true,
            searchable: false,
            // multiple dependant props
            closeOnSelect: false,
            multiple: true,
            hideSelected: true,
        });
    });

    test('expose appropriate props to multiselect component when multiple is false', async () => {

        let $tags = await createVm({
            propsData: {
                multiple: false
            },
            data: () => ({ value: null })
        });

        let { multiselect } = $tags.$refs;

        expect(multiselect.$props).toMatchObject({
            // multiple dependant props
            closeOnSelect: true,
            multiple: false,
            hideSelected: false,
        });
    });

    test('clear on cross button clicked', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            propsData: {
                multiple: false
            },
            methods: {
                inputEmitted
            },
            data: () => ({ value: 3 })
        });

        let clearBtn = document.querySelector('.SharpSelect__clear-button');
        clearBtn.dispatchEvent(new MouseEvent('mousedown'));

        expect(inputEmitted.mock.calls[0][0]).toBe(null);
    });

    test('check correct checkboxes depending on value', async () => {
        let value = [];
        await createVm({
            propsData: {
                multiple: true,
                display: 'list'
            },
            data:() => ({ value })
        });

        let checkboxes = document.querySelectorAll('input');

        expect(document.querySelectorAll('input:checked').length).toBe(0);

        value.push(3);
        await Vue.nextTick();

        expect(checkboxes[0].checked).toBe(true);
        expect(checkboxes[1].checked).toBe(false);
    });

    test('emit input on checkbox changed and correct value', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            propsData: {
                multiple: true,
                display: 'list'
            },
            methods: {
                inputEmitted
            },
            data: () => ({ value: [] })
        });

        let checkbox = document.querySelector('input');

        checkbox.click();

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledWith([3]);
    });

    test('check correct radio depending on value', async () => {

        let { $root:vm } = await createVm({
            propsData: {
                multiple: false,
                display: 'list'
            },
            data: () => ({ value: null }),
        });

        let radios = document.querySelectorAll('input');

        expect(document.querySelectorAll('input:checked').length).toBe(0);

        vm.value = 3;
        await Vue.nextTick();

        expect(radios[0].checked).toBe(true);
        expect(radios[1].checked).toBe(false);
    });

    test('emit input on radio clicked and correct value', async () => {
        let inputEmitted = jest.fn();

        await createVm({
            propsData: {
                multiple: false,
                display: 'list'
            },
            methods: {
                inputEmitted
            },
            data: () => ({ value: null })
        });

        let radio = document.querySelector('input');

        radio.dispatchEvent(new Event('change', { bubbles: true }));

        expect(inputEmitted).toHaveBeenCalledTimes(1);
        expect(inputEmitted).toHaveBeenCalledWith(3);
    });

    test('correct options labels', async () => {
        let $select = await createVm({
            data: () => ({ value: null })
        });

        expect($select.multiselectLabel(3)).toBe('AAA');
        expect($select.multiselectLabel(4)).toBe('BBB');
    });

    test('corresponding multiselect options ids', async () => {
        let $select = await createVm({
            data: () => ({ value: null })
        });

        expect($select.multiselectOptions).toEqual([3,4]);
    });

    test('corresponding input id and label for', async () => {
        await createVm({
            propsData: {
                multiple:false,
                display:'list'
            },
            data: () => ({ value: null })
        });

        let radio = document.querySelector('input'),
            label = document.querySelector('label');

        expect(radio.id).toBe('select0');
        expect(label.htmlFor).toBe('select0');
    });
});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [customOptions],

        components: {
            'sharp-select': Select
        },

        props:['multiple', 'display', 'readOnly'],
    });

    await Vue.nextTick();

    return vm.$children[0];
}