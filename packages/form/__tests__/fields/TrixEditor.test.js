import { shallowMount } from '@vue/test-utils';
import TrixEditor from '../../src/components/fields/wysiwyg/TrixEditor.vue';


describe('trix-editor',()=>{
    let wrapper;

    window.Trix = require('trix');

    beforeEach(() => {
        wrapper = shallowMount(TrixEditor, {
            propsData: {
                uniqueIdentifier: 'WYSIWYG_TEST',
                value: {
                    text:'<div>Wysiwyg text</div>'
                },
                toolbar: [
                    'bold', 'italic', '|', 'link'
                ],
                placeholder: 'placeholder',
                height: 199,
            },
            attachToDocument: true
        });
    });

    test('can mount TrixEditor', ()=>{
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount "read only" TrixEditor', ()=>{
        wrapper.setProps({ readOnly:true });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('emit input', ()=>{
        wrapper.find('trix-editor').trigger('trix-change');
        expect(wrapper.emitted().input).toEqual([[{
            text:'<div>Wysiwyg text</div>'
        }]])
    });
});