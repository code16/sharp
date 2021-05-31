import Vuex from 'vuex';
import Notifications from 'vue-notification';
import { shallowMount, createLocalVue, config } from '@vue/test-utils';
import ActionView from '../components/ActionView.vue';
import { showAlert, showConfirm } from "../util/dialogs";
import store from '../store';

config.stubs['transition-group'] = false;

describe('action-view', ()=>{

    const ModalStub = {
        name:'TestModal',
        template:'<div><slot/></div>'
    };

    const localVue = createLocalVue();
    localVue.use(Notifications);
    localVue.use(Vuex);

    function createWrapper(options={}) {
        return shallowMount(ActionView, {
            ...options,
            propsData: {
                ...options.propsData
            },
            stubs: {
                'SharpModal': ModalStub,
                'router-view': true,
            },
            store: new Vuex.Store(store),
            localVue
        })
    }

    function notify(wrapper, ...args) {
        wrapper.vm.$notify(...args);
        // remove notification global data-id to preserve unit
        wrapper.findAll('[data-id]').wrappers.forEach(wrapper=>wrapper.element.removeAttribute('data-id'));
    }

    test('can mount ActionView', ()=>{
        expect(createWrapper().html()).toMatchSnapshot();
    });

    test('can mount ActionView with notification', ()=>{
        const wrapper = createWrapper();
        notify(wrapper, { title:'TITLE', text:'MESSAGE', type:'TYPE' });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount ActionView with error page', ()=>{
        const wrapper = createWrapper({
            data: ()=>({
                showErrorPage: true,
                errorPageData: {
                    status: 404,
                    message: 'ERROR MESSAGE'
                }
            })
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount with multiple modals', ()=>{
        const wrapper = createWrapper();
        showAlert('Modal 1');
        showConfirm('Modal 2');
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('loading', () => {
        const wrapper = createWrapper();
        wrapper.vm.$store.dispatch('setLoading', true);
        expect(wrapper.html()).toMatchSnapshot();
    });

    xtest('handle main modal events', async ()=>{
        const wrapper = createWrapper();
        const modalOptions = { okCallback:jest.fn() };

        showAlert('Modal 1', modalOptions);

        await wrapper.vm.$nextTick();

        let modal = wrapper.find(ModalStub);

        modal.vm.$emit('ok');
        expect(modalOptions.okCallback).toHaveBeenCalled();
    });
});
