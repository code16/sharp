import { mount } from 'vue-test-utils';
import ViewPanel from '../components/list/ViewPanel.vue';

describe('view-panel', ()=>{
    let wrapper;
    beforeEach(()=>{
        wrapper = mount(ViewPanel);
    });

    test('mount ViewPanel', ()=>{
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount "visible" ViewPanel', ()=>{
        wrapper.setProps({ show: true });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount "with content" ViewPanel', ()=>{
        wrapper.setProps({ show: true, content: 'SOME HTML MARKUP' });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('emit "change" to false on clicked background glasspane', ()=>{
        wrapper.setProps({ show: true });
        let glasspane = wrapper.find('.SharpViewPanel__glasspane');
        glasspane.trigger('click');
        expect(wrapper.emitted().change).toEqual([[false]]);
    });

});