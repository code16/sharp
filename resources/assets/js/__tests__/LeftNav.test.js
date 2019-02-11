import { mount } from '@vue/test-utils';
import LeftNav from '../components/menu/LeftNav.vue';
import Vue from 'vue';
jest.useFakeTimers();



describe('left-nav', ()=>{
    function createWrapper(options) {
        let wrapper = mount(LeftNav, {
            slots: {
                default: '<div>NAV CONTENT</div>'
            },
            propsData: {
                items: [{ entities:[] }]
            },
            created() {
                jest.spyOn(this,'updateState');
            },
            ...options
        });
        jest.runAllTimers();
        return wrapper;
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
        test('is immediate', ()=>{
            expect(createWrapper().vm.$options.watch.collapsed.immediate).toBe(true);
        });

        test('updating state for intermediate animations and set root class', async ()=>{
            const wrapper = createWrapper({ sync:false });
            const collapsedClass = 'leftNav--collapsed';
            const { $root } = wrapper.vm;

            $root.$emit = jest.fn();

            await Vue.nextTick();
            expect($root.$emit).not.toHaveBeenCalledWith('setClass', collapsedClass, expect.anything());
            expect(wrapper.vm.updateState).toHaveBeenCalledTimes(1);

            $root.$emit.mockClear();
            wrapper.setData({ collapsed: true });

            await Vue.nextTick();
            expect($root.$emit).toHaveBeenCalledWith('setClass', collapsedClass, true);
            expect(setTimeout).toHaveBeenCalledWith(wrapper.vm.updateState, 250); // update state called at the end of the animation
            expect(wrapper.vm.state).toBe('collapsing');

            $root.$emit.mockClear();
            wrapper.setData({ collapsed: false });

            await Vue.nextTick();
            expect($root.$emit).toHaveBeenCalledWith('setClass', collapsedClass, false);
            expect(wrapper.vm.state).toBe('expanding');

            jest.runOnlyPendingTimers();
            expect(wrapper.vm.updateState).toHaveBeenCalledTimes(3);
        });
    });

    test('flattenedItems', ()=>{
        const wrapper = createWrapper();
        wrapper.setProps({
            items: [
                { entities:[{ id:1 }], type:'category' },
                { id: 2 },
            ]
        });
        expect(wrapper.vm.flattenedItems).toEqual([{ id:1 }, { id: 2 }]);
    });

    test('currentIcon', ()=>{
        let wrapper = createWrapper();
        wrapper.setProps({ current:'dashboard' });
        expect(wrapper.vm.currentIcon).toBe('fa-dashboard');

        wrapper = createWrapper({
            computed: {
               flattenedItems: ()=>[
                    { key:1, icon:'firstIcon' },
                    { key:2, icon:'secondIcon' }
                ]
            }
        });

        wrapper.setProps({ current:1 });
        expect(wrapper.vm.currentIcon).toBe('firstIcon');
        wrapper.setProps({ current:2 });
        expect(wrapper.vm.currentIcon).toBe('secondIcon');
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

    test('mounted hook', ()=>{
        const wrapper = mount({
            render:()=>null,
            data:()=>({
                isViewportSmall: false,
                collapsed: null,
                ready: false
            })
        });
        LeftNav.mounted.call(wrapper.vm);
        expect(wrapper.vm.collapsed).toBe(false);

        wrapper.setData({ isViewportSmall: true });
        LeftNav.mounted.call(wrapper.vm);
        expect(wrapper.vm.collapsed).toBe(true);
    });
});