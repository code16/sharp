import localizeTags from '../Tags';
import localizeSelect from '../Select';
import { mount } from '@vue/test-utils';
import { localeObject } from "../utils";
import { mockInjections } from "./mock";


jest.mock('../../Localization', ()=>({
    lang: jest.fn(key=>key)
}));
jest.mock('../utils', ()=>({
    localeObject: jest.fn(()=>'localeObject')
}));

describe('localize-select', ()=>{
    let wrapper;
    beforeEach(()=>{
        wrapper = mount({
            mixins:[localizeTags],
            render: h => h(null)
        }, {
            provide: mockInjections({ locales:['en', 'fr'], localized: true })
        })
    });

    test('extends Select', ()=>{
        expect(localizeTags.extends).toBe(localizeSelect);
    });

    test('localizeLabel', ()=>{
        expect(wrapper.vm.localizeLabel('label')).toEqual('label');
        wrapper.setProps({
            localized: true,
            locale: 'en'
        });
        expect(wrapper.vm.localizeLabel({ en:'localizedLabel' })).toEqual('localizedLabel');
        expect(wrapper.vm.localizeLabel({ en:null, fr:'label localisé' })).toEqual('form.tags.unknown_label');
    });

    test('localizedTagLabel', ()=>{
        expect(wrapper.vm.localizedTagLabel('label')).toEqual('label');
        expect(localeObject).not.toHaveBeenCalled();
        wrapper.setProps({
            localized: true,
            locale: 'en'
        });

        expect(wrapper.vm.localizedTagLabel('tagLabel')).toEqual('localeObject');
        expect(localeObject).toHaveBeenCalledTimes(1);
        expect(localeObject).toHaveBeenCalledWith({
            locales: ['en', 'fr'],
            resolve: expect.any(Function)
        });
        let { resolve } = localeObject.mock.calls[0][0];
        expect(resolve('en')).toEqual('tagLabel');
        expect(resolve('fr')).toEqual(null);
    });
});