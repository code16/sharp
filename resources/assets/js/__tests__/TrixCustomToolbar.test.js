import { mount } from '@vue/test-utils';
import TrixCustomToolbar from '../components/form/fields/wysiwyg/TrixCustomToolbar.vue';
import Vue from 'vue';
import { MockI18n } from "./utils";

describe('trix-custom-toolbar',()=>{
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