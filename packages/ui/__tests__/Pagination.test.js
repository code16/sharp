import { mount } from '@vue/test-utils';
import Pagination from '../src/components/Pagination.vue';

describe('pagination', ()=>{
    function createWrapper() {
        return mount(Pagination, {
            propsData: {
                value: 1,
                minPageEndButtons: 3,
                totalRows: 100,
                perPage: 10,
            }
        });
    }

    test('mount pagination', ()=>{
        expect(createWrapper().html()).toMatchSnapshot();
    });

    test('mount pagination without end buttons', ()=>{
        const wrapper = createWrapper();
        wrapper.setProps({
            totalRows: 20,
            perPage: 10,
            minPageEndButtons: 3,
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('hideGotoEndPagination', ()=>{
        const wrapper = createWrapper();
        wrapper.setProps({
            totalRows: 20,
            perPage: 10,
            minPageEndButtons: 3,
        });
        expect(wrapper.vm.hideGotoEndButtons).toEqual(true);
    });
});