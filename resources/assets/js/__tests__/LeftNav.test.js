import { mount } from 'vue-test-utils';
import LeftNav from '../components/menu/LeftNav.vue';

jest.useFakeTimers();

describe('left-nav', ()=>{
    let wrapper;
    beforeEach(()=>{
        wrapper = mount(LeftNav, {
            slots: {
                default: '<div>NAV CONTENT</div>'
            },
            propsData: {
                categories: [{ entities:[] }]
            },
            created() {
                jest.spyOn(this,'updateState');
            }
        });
    });

    test('can mount LeftNav', ()=>{
        expect(wrapper.html()).toMatchSnapshot();
    });
    test('can mount "not ready" LeftNav', ()=>{
        wrapper.setData({ ready:false });
        expect(wrapper.html()).toMatchSnapshot();
    });
    test('can mount "with state" LeftNav', ()=>{
        wrapper.setData({ state:'STATE' });
        expect(wrapper.html()).toMatchSnapshot();
    });
    test('can mount "with current icon" LeftNav', ()=>{
        wrapper.setComputed({ currentIcon: 'CURRENT_ICON' });
        expect(wrapper.html()).toMatchSnapshot();
    });

    describe('watch collapsed', ()=>{
        test('is immediate', ()=>{
            expect(wrapper.vm.$options.watch.collapsed.immediate).toBe(true);
        });

        test('updating state for intermediate animations and set root class', ()=>{
            const collapsedClass = 'leftNav--collapsed';
            expect(wrapper.emitted()['setClass']).toHaveLength(1);
            expect(wrapper.vm.updateState).toHaveBeenCalledTimes(1);

            wrapper.setData({ collapsed: true });
            expect(wrapper.emitted()['setClass'][1]).toEqual([collapsedClass, true]);
            expect(setTimeout).toHaveBeenCalledWith(wrapper.vm.updateState, 250); // update state called at the end of the animation
            expect(wrapper.vm.state).toBe('collapsing');

            wrapper.setData({ collapsed: false });
            expect(wrapper.emitted()['setClass'][2]).toEqual([collapsedClass, false]);
            expect(wrapper.vm.state).toBe('expanding');

            jest.runOnlyPendingTimers();
            expect(wrapper.vm.updateState).toHaveBeenCalledTimes(3);
        });
    });

    test('allEntities', ()=>{
        wrapper.setProps({
            categories: [
                { entities:[1] },
                { entities:[2] },
            ]
        });
        expect(wrapper.vm.allEntities).toEqual([1,2]);
    });

    test('currentIcon', ()=>{
        wrapper.setProps({ current:'dashboard' });
        expect(wrapper.vm.currentIcon).toBe('fa-dashboard');

        wrapper.setComputed({
            allEntities: [
                { key:1, icon:'firstIcon' },
                { key:2, icon:'secondIcon' }
            ]
        });
        wrapper.setProps({ current:1 });
        expect(wrapper.vm.currentIcon).toBe('firstIcon');
        wrapper.setProps({ current:2 });
        expect(wrapper.vm.currentIcon).toBe('secondIcon');
    });

    test('updateState', ()=>{
        wrapper.setData({ collapsed: true });
        wrapper.vm.updateState();
        expect(wrapper.vm.state).toBe('collapsed');

        wrapper.setData({ collapsed: false });
        wrapper.vm.updateState();
        expect(wrapper.vm.state).toBe('expanded');
    });

    test('mounted hook', ()=>{
        let wrapper = mount({
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