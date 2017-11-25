import FilterSelect from '../components/list/FilterSelect.vue';
import Select from '../components/form/fields/Select.vue';
import { mount, shallow } from 'vue-test-utils';


describe('filter-select', ()=>{
    let wrapper, select;

    beforeEach(()=>{
        wrapper = shallow(FilterSelect, {
            propsData : {
                filterKey: 'job',
                name:'Web job',
                values: [
                    { id:1, label:'front' },
                    { id:2, label:'back' },
                ]
            }
        });
        select = wrapper.find(Select);
    });

    test('can mount "empty" FilterSelect', ()=>{
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount "valued" FilterSelect', ()=>{
        wrapper.setProps({
            value: 1 
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount "multiple empty" FilterSelect', ()=>{
        wrapper.setProps({
            multiple: true,
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount "multiple valuated" FilterSelect', ()=>{
        wrapper.setProps({
            multiple: true,
            value: [1,2]
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('has select', ()=>{
        expect(select.isVueInstance()).toBe(true);
    });

    test('expose appropriate props to select', ()=>{
        wrapper.setProps({
            multiple: true,
            required: true,
            value: [1,2]
        });
        expect(select.vm.$props).toMatchObject({
            value: [1,2],
            options: [
                { id:1, label:'front' },
                { id:2, label:'back' },
            ],
            multiple: true,
            clearable: false,
            inline: false,
            uniqueIdentifier: 'job'
        });
    });

    test('call appropriate handlers', ()=>{
        let label = wrapper.find('.SharpFilterSelect__text');

        wrapper.setMethods({
            handleSelect: jest.fn(),
            showMultiselect: jest.fn()
        });
        select.vm.$emit('input');
        expect(wrapper.vm.handleSelect).toHaveBeenCalled();
        label.trigger('click');
        expect(wrapper.vm.showMultiselect).toHaveBeenCalled();
    });

    test('bind opened', ()=>{
        expect(wrapper.vm.opened).toBe(false);
        select.vm.$emit('open');
        expect(wrapper.vm.opened).toBe(true);
        select.vm.$emit('close');
        expect(wrapper.vm.opened).toBe(false);
    });

    test('empty', ()=>{
        expect(wrapper.vm.empty).toBe(true);
        wrapper.setProps({ value:1 });
        expect(wrapper.vm.empty).toBe(false);
        wrapper.setProps({ value:[], multiple:true });
        expect(wrapper.vm.empty).toBe(true);
        wrapper.setProps({ value:[1], multiple:true });
        expect(wrapper.vm.empty).toBe(false);
    });

    test('emit input', ()=>{
        wrapper.vm.handleSelect(1);
        expect(wrapper.emitted('input')).toEqual([[1]]);
    });
});