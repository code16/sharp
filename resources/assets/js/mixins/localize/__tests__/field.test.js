import localizeField from '../field';
import { mount } from '@vue/test-utils';
import { mockInjections } from "./mock";

describe('localize-field', ()=>{
    let wrapper;
    beforeEach(()=>{
        wrapper = mount({
            mixins: [localizeField],
            render: h => h(null)
        }, {
            propsData: { locale: 'fr', localized: false },
            provide: mockInjections({ locales:['en', 'fr'], localized: false })
        });
    });

    test('has locales', ()=>{
        expect(wrapper.vm.locales).toEqual(['en', 'fr']);

        wrapper.vm.$form.locales = ['en'];

        expect(wrapper.vm.locales).toEqual(['en']);
    });

    test('isLocalized', ()=>{
        expect(wrapper.vm.isLocalized).toEqual(false);
        wrapper.vm.$form.localized = true;
        expect(wrapper.vm.isLocalized).toEqual(false);
        wrapper.setProps({ localized:true });
        expect(wrapper.vm.isLocalized).toEqual(true);
    });

    test('has props', ()=>{
        expect(wrapper.vm.$props).toMatchObject({ locale:'fr', localized:false });
    });
});