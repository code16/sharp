import localizeField from '../field';
import { mount } from 'vue-test-utils';
import Vue from 'vue';

describe('localize-field', ()=>{
    let wrapper;
    beforeEach(()=>{
        wrapper = mount({
            mixins: [localizeField]
        }, {
            propsData: { locale: 'fr', localized: true },
            provide: {
                $form: new Vue({ data:()=>({ locales:['en', 'fr'] })})
            }
        });
    });

    test('has locales', ()=>{
        expect(wrapper.vm.locales).toEqual(['en', 'fr']);

        wrapper.vm._provided.$form.locales = ['en'];

        expect(wrapper.vm.locales).toEqual(['en']);
    });

    test('has props', ()=>{
        expect(wrapper.vm.$props).toMatchObject({ locale:'fr', localized:true });
    });
});