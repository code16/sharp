import Vuex from 'vuex';
import { mount, createLocalVue } from '@vue/test-utils';
import globalFiltersModule from '@sharp/filters/src/store/global-filters';
import LeftNav from '../components/LeftNav.vue';

jest.mock('sharp');
jest.mock('sharp-filters/src/store/global-filters');
jest.useFakeTimers();

describe('left-nav', ()=>{
    function createWrapper(options) {
        const localVue = createLocalVue();
        localVue.use(Vuex);

        const store = new Vuex.Store({
            modules: {
                'global-filters': globalFiltersModule,
            },
        });
        store.dispatch = jest.fn();

        return mount(LeftNav, {
            slots: {
                default: '<div>NAV CONTENT</div>'
            },
            created() {
                jest.spyOn(this, 'updateState');
            },
            localVue,
            store,
            ...options
        });
    }

    test('can mount LeftNav', async ()=>{
        const wrapper = createWrapper({ sync:false });

        await wrapper.vm.$nextTick();
        expect(wrapper.html()).toMatchSnapshot();
    });
    test('can mount "not ready" LeftNav', async ()=>{
        const wrapper = createWrapper({ sync:false });
        await wrapper.vm.$nextTick();

        wrapper.setData({ ready:false });
        await wrapper.vm.$nextTick();
        expect(wrapper.html()).toMatchSnapshot();
    });
    test('can mount "with state" LeftNav', async ()=>{
        const wrapper = await createWrapper({ sync:false });

        wrapper.setData({ state:'STATE' });
        await wrapper.vm.$nextTick();
        expect(wrapper.html()).toMatchSnapshot();
    });
    test('can mount "with current icon" LeftNav', async ()=>{
        let wrapper = createWrapper({ computed:{ currentIcon:()=>'CURRENT_ICON' }, sync: false });

        await wrapper.vm.$nextTick();
        expect(wrapper.html()).toMatchSnapshot();
    });


    describe('watch collapsed', ()=>{
        test('updating state for intermediate animations and set root class', async ()=>{
            const wrapper = createWrapper({ sync:false });

            await wrapper.vm.$nextTick();

            wrapper.setData({ collapsed: true });

            await wrapper.vm.$nextTick();
            // expect(setTimeout).toHaveBeenCalledWith(wrapper.vm.updateState, 250); // update state called at the end of the animation
            expect(wrapper.vm.state).toBe('collapsing');

            wrapper.setData({ collapsed: false });

            await wrapper.vm.$nextTick();
            expect(wrapper.vm.state).toBe('expanding');

            jest.runOnlyPendingTimers();
            expect(wrapper.vm.updateState).toHaveBeenCalledTimes(2);
        });
    });

    test('updateState', ()=>{
        const wrapper = createWrapper();
        wrapper.setData({ collapsed: true });
        wrapper.vm.updateState();
        expect(wrapper.vm.state).toBe('collapsed');

        wrapper.setData({ collapsed: false });
        wrapper.vm.updateState();
        expect(wrapper.vm.state).toBe('expanded');
    });
});
