import { mount } from '@vue/test-utils';
import LeftNav from '../components/menu/LeftNav.vue';
import Vue from 'vue';
jest.useFakeTimers();



describe('left-nav', ()=>{
    let wrapper;
    function createWrapper(options) {
        let vm = mount(LeftNav, {
            slots: {
                default: '<div>NAV CONTENT</div>'
            },
            propsData: {
                menuItems: [{ entities:[] }]
            },
            created() {
                jest.spyOn(this,'updateState');
            },
            ...options
        });
        vm.setData({ ready:true });
        jest.runAllTimers();
        jest.runAllTicks();
        return vm;
    }
    beforeEach(()=>{

    });

    test('can mount LeftNav', ()=>{
        expect(createWrapper().html()).toMatchSnapshot();
    });
    test('can mount "not ready" LeftNav', ()=>{
        const wrapper = createWrapper();
        wrapper.setData({ ready:false });
        expect(wrapper.html()).toMatchSnapshot();
    });
    test('can mount "with state" LeftNav', ()=>{
        const wrapper = createWrapper();
        wrapper.setData({ state:'STATE' });
        expect(wrapper.html()).toMatchSnapshot();
    });
    test('can mount "with current icon" LeftNav', ()=>{
        let wrapper = createWrapper({ computed:{ currentIcon:()=>'CURRENT_ICON' }});
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

    test('allEntities', ()=>{
        const wrapper = createWrapper();
        wrapper.setProps({
            categories: [
                { entities:[1] },
                { entities:[2] },
            ]
        });
        expect(wrapper.vm.allEntities).toEqual([1,2]);
    });

    test('currentIcon', ()=>{
        let wrapper = createWrapper();
        wrapper.setProps({ current:'dashboard' });
        expect(wrapper.vm.currentIcon).toBe('fa-dashboard');

        wrapper = createWrapper({
            computed: {
                allEntities:()=>[
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