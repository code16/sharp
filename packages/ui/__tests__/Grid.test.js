import Grid from '../src/components/Grid.vue';
import { mount } from '@vue/test-utils';

describe('grid',()=>{
    let wrapper, baseOptions;
    beforeEach(()=>{
        wrapper = mount(Grid, baseOptions={
            propsData: {
                rows: [
                    [{ size: 6, sizeXS: 12 }, { size: 4 }],
                    [{ size: 10 }, { size: null }] // should have class col-sm
                ]
            }
        });
    });

    test('can mount Grid', ()=>{
        expect(wrapper.html()).toMatchSnapshot();
    })

});