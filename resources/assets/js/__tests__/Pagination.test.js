import { mount } from 'vue-test-utils';
import Pagination from '../components/list/Pagination.vue';

describe('pagination', ()=>{
    function createPagination(props={}) {
        return mount(Pagination, {
            context: {
                attrs: {},
                props: {
                    minPageEndButtons: 3,
                    totalRows: 100,
                    perPage: 10,
                    ...props
                }
            }
        });
    }

    test('mount pagination', ()=>{
        expect(createPagination().html()).toMatchSnapshot();
    });

    test('mount pagination without end buttons', ()=>{
        expect(createPagination({ totalRows: 20 }).html()).toMatchSnapshot();
    });
});