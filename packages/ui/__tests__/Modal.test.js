import { mount } from '@vue/test-utils';
import SharpModal from '../src/components/Modal.vue';

jest.mock('sharp/mixins/Localization');


describe('modal', ()=>{

    function bModal(wrapper) {
        return wrapper.find({ ref:'modal' });
    }

    test('mount', ()=>{
        const wrapper = mount(SharpModal, {
            propsData: {
                id: 'modal-id',
                cancelTitle: 'cancel title',
                okTitle: 'ok title',
                okOnly: false,
                title: 'title',
                static: true,
            },
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount ok only', ()=>{
        const wrapper = mount(SharpModal, {
            propsData: {
                okOnly: true,
                static: true,
            }
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount visible', async ()=>{
        const wrapper = mount(SharpModal, {
            propsData: {
                static: true,
            },
        });
        wrapper.setProps({ visible:true });
        await wrapper.vm.$nextTick();
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount with modifiers classes', ()=>{
        const wrapper = mount(SharpModal, {
            propsData: {
                isError: true,
                static: true,
            }
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('pass props', () => {
        const wrapper = mount(SharpModal, {
            propsData: {
                id: 'modal-id',
            }
        });

        expect(bModal(wrapper).props()).toMatchObject({
            id: 'modal-id',
            visible: false,
            okOnly: false,
            noEnforceFocus: true,
        });
        wrapper.setProps({
            visible: true,
            cancelTitle: 'custom cancel title',
            okTitle: 'custom ok title',
            okOnly: true,
            title: 'custom title',
        });
        expect(bModal(wrapper).props()).toMatchObject({
            visible: true,
            okOnly: true,
            title: 'custom title',
        });
    });

    test('show/hide methods and emit updates', async ()=>{
        const wrapper = mount(SharpModal, {
            propsData:{
                static: true,
            },
            sync: false
        });
        wrapper.vm.show();
        await wrapper.vm.$nextTick();
        expect(wrapper.find('.modal').isVisible()).toBe(true);
        wrapper.vm.hide();
        await wrapper.vm.$nextTick();
        expect(wrapper.find('.modal').isVisible()).toBe(false);
    });

    test('emit updates', async ()=>{
        const wrapper = mount(SharpModal, { sync: false });
        wrapper.vm.show();
        await wrapper.vm.$nextTick();
        expect(wrapper.emitted()['update:visible'][0]).toEqual([true]);
        // update visible prop manually like "update:" event handler
        wrapper.setProps({
            visible: true,
        });
        await wrapper.vm.$nextTick();
        wrapper.vm.hide();
        await wrapper.vm.$nextTick();
        expect(wrapper.emitted()['update:visible'][1]).toEqual([false]);
    });

    test('pass props to b-modal', ()=>{
        const wrapper = mount({
            template: '<SharpModal testProp="test" />',
            components: { SharpModal }
        });
        expect(bModal(wrapper.find(SharpModal)).vm.$attrs).toMatchObject({
            testProp: 'test'
        });
    });

    test('pass listeners to b-modal', ()=>{
        const wrapper = mount({
            template: '<SharpModal @testListener="()=>{}" />',
            components: { SharpModal }
        });
        expect(bModal(wrapper.find(SharpModal)).vm.$listeners).toMatchObject({
            testListener: expect.any(Function)
        })
    });
});
