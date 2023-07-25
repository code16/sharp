import { LocalizedEditor } from '../../../src/mixins/localize/editor';
import { localeObjectOrEmpty } from "../../../src/util/locale";
import { mount } from '@vue/test-utils';
import { mockInjections } from "./mock";

jest.mock('../../../src/util/locale', ()=>({
    localeObjectOrEmpty:jest.fn(()=>'localeObjectOrEmpty')
}));

describe('localize-editor', ()=>{
    let wrapper;
    beforeEach(() => {
        wrapper = mount({
            mixins:[LocalizedEditor],
            data() {
                return {
                    value: { text: null }
                }
            },
            render() {}
        }, {
            propsData: { localized:false, locale:'en' },
            provide: mockInjections({ locales:['en', 'fr'], localized: true })
        });
    });

    test('localizedText', ()=>{
        expect(wrapper.vm.localizedText).toEqual(null);
        wrapper.setProps({
            localized: true
        });
        expect(wrapper.vm.localizedText).toEqual(null);
        wrapper.setData({
            value: { text: { en:'english text' } }
        });
        expect(wrapper.vm.localizedText).toEqual('english text');
    });

    test('localizedValue', ()=>{
        expect(wrapper.vm.localizedValue('aaa')).toEqual({
            text: 'aaa'
        });
        expect(localeObjectOrEmpty).not.toHaveBeenCalled();
        wrapper.setProps({
            localized: true
        });
        wrapper.setData({
            value: { text: { fr:'coucou' }, files:[] }
        });
        expect(wrapper.vm.localizedValue('aaa', 'en')).toEqual({ text: 'localeObjectOrEmpty', files:[] });
        expect(localeObjectOrEmpty).toHaveBeenCalledWith({ localeObject: { fr:'coucou' }, locale: 'en', value: 'aaa' });
    });

});
