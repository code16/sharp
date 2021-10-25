import FilterSelect from '../src/components/filters/FilterSelect.vue';
import { Select } from 'sharp-form';
import { shallowMount } from '@vue/test-utils';
import { MockI18n } from "@sharp/test-utils";
import Vue from 'vue';

jest.mock('sharp');

describe('filter-select', ()=>{
    Vue.use(MockI18n, { mockFn:true });

    function createWrapper(options={}) {
        return shallowMount(FilterSelect, {
            ...options,
            propsData : {
                filterKey: 'job',
                label: 'Web job',
                values: [
                    { id:1, label:'front' },
                    { id:2, label:'back' },
                ],
                ...options.propsData
            },
        });
    }

    function findSelect(wrapper) {
        return wrapper.find(Select);
    }

    test('can mount "empty" FilterSelect', ()=>{
        expect(createWrapper().html()).toMatchSnapshot();
    });

    test('can mount "valuated" FilterSelect', ()=>{
        expect(createWrapper({propsData: { value:1 }}).html()).toMatchSnapshot();
    });

    test('can mount "multiple empty" FilterSelect', ()=>{
        expect(createWrapper({ propsData:{multiple:true} }).html()).toMatchSnapshot();
    });

    test('can mount "multiple valuated" FilterSelect', ()=>{
        expect(createWrapper({ propsData:{multiple:true, value: [1,2]} }).html()).toMatchSnapshot();
    });

    test('has select', ()=>{
        expect(findSelect(createWrapper()).isVueInstance()).toBe(true);
    });

    test('expose appropriate props to select', ()=>{
        const wrapper = createWrapper({
            propsData: {
                multiple: true,
                required: true,
                value: [1,2]
            }
        });
        const select = findSelect(wrapper);

        expect(select.vm.$props).toMatchObject({
            value: [1,2],
            options: [
                { id:1, label:'front' },
                { id:2, label:'back' },
            ],
            multiple: true,
            clearable: false,
            inline: false,
        });
    });

    test('call appropriate handlers', async ()=>{
        const wrapper = createWrapper({
            created() {
                this.handleSelect = jest.fn();
            }
        });
        const select = findSelect(wrapper);

        select.vm.$emit('input');
        expect(wrapper.vm.handleSelect).toHaveBeenCalled();
    });

    test('empty', ()=>{
        const wrapper = createWrapper();

        expect(wrapper.vm.empty).toBe(true);
        wrapper.setProps({ value:1 });
        expect(wrapper.vm.empty).toBe(false);
        wrapper.setProps({ value:[], multiple:true });
        expect(wrapper.vm.empty).toBe(true);
        wrapper.setProps({ value:[1], multiple:true });
        expect(wrapper.vm.empty).toBe(false);
    });

    test('emit input', ()=>{
        const wrapper = createWrapper();

        wrapper.vm.handleSelect(1);
        expect(wrapper.emitted('input')).toEqual([[1]]);
    });
});
