import { mount } from '@vue/test-utils';
import localizeForm from '../../../src/mixins/localize/form';
import { isLocalizableValueField, localeObjectOrEmpty } from "../../../src/util/locale";


jest.mock('../../../src/util/locale', ()=>({
    isLocalizableValueField: jest.fn(()=>true),
    localeObjectOrEmpty: jest.fn(()=>'localeObjectOrEmpty')
}));


describe('localize-form', ()=>{
    let wrapper;
    beforeEach(()=>{
        wrapper = mount({
            mixins:[localizeForm('fields')],
            data() {
                return {
                    data: { title:'myTitle' },
                    fields: {
                        title: { type:'text', localized: false }
                    },
                    fieldLocale: { title:'en' },
                    localized: true
                }
            },
            render() {}
        });
    });

    test('has localizedForm option', ()=>{
        expect(wrapper.vm.$options._localizedForm).toBe('fields');
    });

    test('fieldLocalizedValue', () => {
        expect(wrapper.vm.fieldLocalizedValue('title', 'ABC')).toEqual('ABC');
        expect(isLocalizableValueField).not.toHaveBeenCalled();
        expect(localeObjectOrEmpty).not.toHaveBeenCalled();

        wrapper.vm.fields.title.localized = true;
        wrapper.vm.data.title = { en:'myTitle' };

        expect(wrapper.vm.fieldLocalizedValue('title', 'ABC')).toEqual('localeObjectOrEmpty');
        expect(isLocalizableValueField).toHaveBeenCalledWith(expect.objectContaining({ type:'text'}));
        expect(localeObjectOrEmpty).toHaveBeenLastCalledWith({ localeObject: { en:'myTitle' }, locale:'en', value:'ABC' });

        expect(wrapper.vm.fieldLocalizedValue('title', 'ABC', { title: { fr:'monTitre' } })).toEqual('localeObjectOrEmpty');
        expect(localeObjectOrEmpty).toHaveBeenLastCalledWith({ localeObject: { fr:'monTitre' }, locale:'en', value:'ABC' });
    })
});
