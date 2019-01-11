import localizeAutocomplete from '../Autocomplete';
import localizeField from '../field';
import { mount } from '@vue/test-utils';

import { mockInjections } from "./mock";

describe('localize-autocomplete', ()=>{
    let wrapper;
    beforeEach(()=>{
        wrapper = mount({
            mixins: [localizeAutocomplete],
            data: ()=>({
                searchKeys:['title', 'label'],
                localValues: []
            }),
            render: h => h(null)
        }, {
            propsData: { localized: false, locale:'en'  },
            provide: mockInjections({ locales: ['en', 'fr'], localized:true })
        });
    });

    test('has field mixin', ()=>{
        expect(localizeAutocomplete.mixins).toContain(localizeField);
    });

    test('localizedSearchKeys', ()=>{
        Object.defineProperty(wrapper.vm, 'localizedDataKeys', { get:()=>['title'] });
        expect(wrapper.vm.localizedSearchKeys).toEqual(['title', 'label']);
        wrapper.setProps({ localized: true });
        expect(wrapper.vm.localizedSearchKeys).toEqual(['title.en', 'label']);
        wrapper.setProps({ locale: 'fr' });
        expect(wrapper.vm.localizedSearchKeys).toEqual(['title.fr', 'label']);
    });

    test('localizedDataKeys', ()=>{
        expect(wrapper.vm.localizedDataKeys).toEqual([]);
        wrapper.setData({
            localValues: [{ title: { fr: 'FR text', en: 'EN text' }, label: 'aaa' }]
        });
        expect(wrapper.vm.localizedDataKeys).toEqual(['title']);
    });

    test('localizedTemplateData', ()=>{
        expect(wrapper.vm.localizedTemplateData({ title: 'aaa', label:'bbb' })).toEqual({ title: 'aaa', label:'bbb' });
        wrapper.setProps({ localized: true });
        expect(wrapper.vm.localizedTemplateData({ title: { fr:'FR text', en: 'EN text' }, label:'bbb' })).toEqual({ title: 'EN text', label:'bbb' });
    });
});