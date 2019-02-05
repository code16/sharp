import localizeSelect from '../Select';
import localizeField from '../field';
import { mount } from '@vue/test-utils';
import { mockInjections } from "./mock";

describe('localize-select', ()=>{
    let wrapper;
    beforeEach(()=>{
        wrapper = mount({
            mixins:[localizeSelect],
            render: h => h(null)
        }, { provide:mockInjections({ localized:true }) })
    });

    test('has field mixin', ()=>{
        expect(localizeSelect.mixins).toContain(localizeField);
    });

    test('localizeLabel', ()=>{
        expect(wrapper.vm.localizeLabel('label')).toEqual('label');
        wrapper.setProps({
            localized: true,
            locale: 'en'
        });
        expect(wrapper.vm.localizeLabel({ en:'localizedLabel' })).toEqual('localizedLabel');
    });

    test('localizeOptionLabel', ()=>{
        wrapper.vm.localizeLabel = jest.fn();
        wrapper.vm.localizedOptionLabel({ label: 'label' });
        expect(wrapper.vm.localizeLabel).toHaveBeenCalledWith('label');
    });
});