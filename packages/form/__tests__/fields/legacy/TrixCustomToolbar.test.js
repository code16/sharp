import { mount } from '@vue/test-utils';
import Vue from 'vue';
// import TrixCustomToolbar from '../../src/components/fields/wysiwyg/TrixCustomToolbar.vue';
import { MockI18n } from "@sharp/test-utils";

xdescribe('trix-custom-toolbar',()=>{
    let wrapper;
    Vue.use(MockI18n, { mockFn: true });

    beforeEach(()=>{
        wrapper = mount(TrixCustomToolbar, {
            propsData : {
                toolbar: ['bold', 'italic', '|', 'link']
            },
            attachToDocument: true
        });
    });

    test('can mount TrixCustomToolbar', ()=>{
        expect(wrapper.html()).toMatchSnapshot();
    });
});
