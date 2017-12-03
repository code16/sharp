import { shallow, mount } from 'vue-test-utils';
import Pagination from '../components/list/Pagination.vue';

import * as BVCompModule from '../mixins/BVComp';

BVCompModule.default = name => ({ name, render() { return this._v(`BVCOMP__${name}`) } });

// jest.mock('../mixins/BVComp.js', ()=>{
//     return name => ({ name, render() { return this._v(`BVCOMP__${name}`) } });
// });

jest.mock('bootstrap-vue');

describe('pagination', ()=>{
    let wrapper;
    beforeEach(()=>{
        wrapper = shallow(Pagination, {
            context: {
                data: {
                    attrs: {}
                }
            }
        });
    });

    xtest('mount pagination', ()=>{
        test(wrapper.html()).toMatchSnapshot();
    });
});